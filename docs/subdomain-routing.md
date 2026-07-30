# サブドメインによるミニアプリ振り分け

`*.ruu-dev.com` の各サブドメイン(`memo.`、`dockerfiles.`、`post.` など)を、1つのLaravelアプリだけで振り分けている仕組みについて。

## インフラ構成

- DNSは `*.ruu-dev.com` のワイルドカードで、すべて同じConoHa WINGのオリジンを向く
- 手前にCloudflare Workerがいて、オリジンへ転送する際に **Hostを`laravel.ruu-dev.com`に書き換え**、実際にアクセスされた元のサブドメイン名を `X-App-Subdomain` ヘッダーに入れて渡す
- そのため、Laravel側では `$request->getHost()` は常に `laravel.ruu-dev.com` にしかならず、`Route::domain()` は使えない。**サブドメインの判定はヘッダー経由**で行う

## なぜ「起動時に該当アプリのルートだけ読み込む」ができないか

直感的には、ヘッダーを見て `routes/apps/{app}.php` だけを `require` すれば良さそうに見えるが、これは過去に実際に試して壊れた。

Laravelのルート登録(`routes/web.php`)は **アプリケーションのboot時に1回だけ** 実行される。これは「リクエストのたびに新しいPHPプロセスが立つ」古典的なPHP-FPM環境ではリクエストごとに再実行されているように"見える"が、以下の場面では前提が崩れる。

- **テスト(`tests/`)**: 1つのテストメソッド内で `$this->get('/a')` → `$this->get('/b')` のように複数リクエストを送っても、アプリケーションは1回しかbootされず使い回される。最初のリクエストで読み込んだルートファイルが、後続の別サブドメイン宛のリクエストにも使われ続けてしまう
- **Octaneなどの永続ワーカー**: 同様にboot結果が使い回される

「ヘッダーに応じてどのルートを使うか」は**リクエストごとに変わる判断**であり、「boot」という**アプリケーションの寿命に1回しかないタイミング**に混ぜてはいけない。この2つの時間軸を混同したのが最初の実装ミスだった。

## 採用した設計: 全部登録 + prefix + ミドルウェアでパス書き換え

- `routes/web.php` は、判断や分岐を一切せず、**全ミニアプリのルートを機械的に全部登録するだけ**(何回bootされても結果は同じ → テスト・Octaneどちらでも安全)
- 各アプリのルートは `Route::prefix($app)` で登録され、実際のURIは `/{app}/...` になる(例: `post`アプリの `/` は実体としては `/post`)
- **リクエストごとに変わる判断(=どのサブドメイン向けか)は、本当に毎回実行されるミドルウェア `App\Http\Middleware\ResolveAppSubdomain` だけに閉じ込める**
  - `X-App-Subdomain` ヘッダー(本番)、または `*.localhost` のHost(ローカル限定の利便性)からアプリ名を判定
  - 判定結果を使い、**受信リクエストのパスの先頭に `/{app}` を足してから** ルーティングに渡す(`Request::create()` で新しいRequestを作り直す。元のRequestは書き換えない)
  - この prefix はサーバー内部だけのものであり、ブラウザ側のURLには一切登場しない

```
ブラウザ: GET /1  (Host: post.ruu-dev.com 相当、実際はWorker経由でHostはlaravel.ruu-dev.com、X-App-Subdomain: post)
   ↓ ResolveAppSubdomainミドルウェア
サーバー内部: GET /post/1  ← ここで初めてprefixが付く
   ↓ ルーティング
/post/{id} にマッチ → PostController@show
```

## ハマった点1: `route()`が生成するURLに勝手にprefixが付く

`route('posts.show', $id)` はミドルウェアを一切通らない。ルート表に直接「`posts.show`という名前のURIは?」と聞くだけなので、`Route::prefix('post')`込みの **`/post/1`** がそのまま返ってくる(嘘ではなく、実際そう登録されているので正しい回答)。

ミドルウェアは**受信(ブラウザ→サーバー)**方向にしかprefixの後始末をしていないため、`route()`が返す**送信(サーバーが生成するリンク)**方向のURLにはprefixが残ったままになる。

```
route('posts.show', 1) → "/post/1"          ← prefix付きのまま
ブラウザがこれをクリック → post.ruu-dev.com/post/1 にアクセス
   ↓ ミドルウェアがまた /post を足す
サーバー内部: /post/post/1  ← 二重prefix、該当ルートなし → 404
```

### 対応: `App\Routing\SubdomainAwareUrlGenerator`

Laravelの `url` コンテナバインディングをこのクラスに差し替えている([app/Providers/AppServiceProvider.php](../app/Providers/AppServiceProvider.php))。`route()` / `action()` の戻り値から、現在のリクエストが属するアプリのprefix部分を剥がしてから返す。これによりビュー・コントローラーはprefixの存在を一切意識せず、普段どおり `route()` を呼べる。

## ハマった点2: 「アプリの一覧」が2箇所に分裂して食い違った

以前は `ResolveAppSubdomain::APPS` という手動維持の配列(ホワイトリスト)を、`routes/web.php` のループと、ヘッダーの正当性チェックの両方で参照していた。

新しいミニアプリ(`post`)を追加した際、**`routes/apps/post.php` などの実体ファイルをコミットし忘れたまま、`APPS` 配列への追加だけが先にコミットされてしまった**。その結果、本番デプロイ後に `routes/web.php` のループが存在しないファイルを `require` しようとして例外が発生し、**起動処理そのものが失敗 → `post` だけでなく `memo` など全サブドメインが500になった**(1つのLaravelアプリで全部を賄っているため、boot失敗は道連れになる)。

### 対応: ファイルシステムを唯一の情報源にする

`ResolveAppSubdomain::apps()` が `routes/apps/*.php` を実際にスキャンして一覧を作る([app/Http/Middleware/ResolveAppSubdomain.php](../app/Http/Middleware/ResolveAppSubdomain.php))。手動で維持する配列は廃止した。

- `routes/web.php` のループも、ヘッダーのホワイトリストチェックも、**同じ `apps()` の結果を参照する**ようにしたので、「一覧には書いたが実体がない」という食い違いはそもそも起こり得ない
- ファイルが存在しないサブドメインは、単にそのサブドメインだけが404になる(他のアプリは無関係に動き続ける)

## 新しいミニアプリの追加方法

1. `routes/apps/{subdomain}.php` を作成し、通常どおり `Route::get('/', ...)` 等を書く(このファイル内のパスは、そのアプリのprefixからの相対パスでよい。`Route::prefix()`は`routes/web.php`側で自動的に付与される)
2. コントローラー・ビュー・(必要なら)モデル/マイグレーションを追加する
3. コミット・pushする(**手順はこれだけ**。ホワイトリストを手動で書き換える必要はない — [apps()](../app/Http/Middleware/ResolveAppSubdomain.php)が`routes/apps/`ディレクトリを自動でスキャンする)

`laravel.ruu-dev.com` を訪れた場合や、`X-App-Subdomain`ヘッダーが付かない直接アクセスの場合は `ResolveAppSubdomain::DEFAULT_APP`(`"laravel"`)にフォールバックする。

## ローカルでの確認方法

- `http://{app}.localhost`(例: `http://post.localhost`)にアクセスすると、`*.localhost`はDNS設定不要でloopbackに解決されるため、ヘッダーを手動で付けずに動作確認できる(`app()->environment('local')`限定の挙動。本番のHostは常に`laravel.ruu-dev.com`なので、この経路は本番では使われない)
- ヘッダーを直接指定したい場合: `curl -H "X-App-Subdomain: post" http://localhost/1`
