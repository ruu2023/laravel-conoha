# ミニアプリ振り分け: パスベース方式(旧サブドメイン方式からの移行)

`ruu-dev.com` の各パス(`/memo`、`/dockerfiles`、`/post` など)を、1つのLaravelアプリだけで振り分けている仕組みについて。

以前はサブドメイン(`memo.ruu-dev.com` など)で振り分けていたが、2026-08にパスベースへ移行した。理由はSEO/ドメイン評価の集約と管理コストの低減(詳細は本ドキュメント末尾の「サブドメイン方式からの移行」を参照)。

## 採用した設計: 全部登録 + prefix、パスがそのまま公開URL

- `routes/web.php` は、判断や分岐を一切せず、**全ミニアプリのルートを機械的に全部登録するだけ**(何回bootされても結果は同じ → テスト・Octaneどちらでも安全)。`routes/apps/*.php`をglobして一覧を作り(ファイルシステムが唯一の情報源)、`config('apps.disabled')`/`config('app.disabled_apps')`で無効化されたものを差し引いてループする — このロジック自体が`routes/web.php`に直接書かれており、専用のクラスやミドルウェアは存在しない
- 各アプリのルートは `Route::prefix($app)` で登録され、実際のURIも公開URLも `/{app}/...` になる(例: `memo`アプリの `/` は `ruu-dev.com/memo`)。**唯一の例外は `root`**(apex Inertiaアプリ)で、`ruu-dev.com/` 自体をprefixなしで受け持つ
- ルーティングに関与するミドルウェアは存在しない。ブラウザが送ってくるパスがそのまま最終的なルーティング対象になり、Laravel標準の`Route::prefix()`マッチングだけで完結する

```
ブラウザ: GET /memo/1
   ↓ ルーティング(パスそのままマッチ、途中に何も挟まらない)
/memo/{id} にマッチ → 対応するコントローラー/ビュー
```

「今どのアプリのリクエストか」を汎用的に知る仕組み(旧`app_subdomain`属性のようなもの)はもう存在しない。それを必要としていた箇所(Google OAuthのコールバックURL生成)は、実際には常に`login`アプリ固定だったので`route('login.xxx')`と直接書くだけで足りると分かり、汎用的な「現在のアプリ」概念ごと削除した。旧サブドメイン方式にあった「受信方向だけプレフィックスを足して、送信方向(`route()`の戻り値)は剥がして辻褄を合わせる」という非対称な仕組みも同様にもう存在しない。

## なぜ「起動時に該当アプリのルートだけ読み込む」ができないか(サブドメイン時代からの不変の理由)

直感的には、リクエストのパスを見て `routes/apps/{app}.php` だけを `require` すれば良さそうに見えるが、これは過去に実際に試して壊れた。この理由はパスベースに移行した今も変わらず有効。

Laravelのルート登録(`routes/web.php`)は **アプリケーションのboot時に1回だけ** 実行される。これは「リクエストのたびに新しいPHPプロセスが立つ」古典的なPHP-FPM環境ではリクエストごとに再実行されているように"見える"が、以下の場面では前提が崩れる。

- **テスト(`tests/`)**: 1つのテストメソッド内で `$this->get('/a')` → `$this->get('/b')` のように複数リクエストを送っても、アプリケーションは1回しかbootされず使い回される。最初のリクエストで読み込んだルートファイルが、後続の別アプリ宛のリクエストにも使われ続けてしまう
- **Octaneなどの永続ワーカー**: 同様にboot結果が使い回される

「どのルートファイルを読み込むか」を**リクエストごとに変わる判断**として「boot」という**アプリケーションの寿命に1回しかないタイミング**に混ぜてはいけない。この2つの時間軸を混同したのが最初の実装ミスだった。だからこそ`routes/web.php`は全アプリのルートを無条件に毎回全部登録する(パスベースに移行してからは、そもそも「今どのアプリか」というリクエストごとに変わる判断自体が routing 以外どこにも残っていない)。

## 「アプリの一覧」の単一情報源

`routes/web.php`が`routes/apps/*.php`を直接globして一覧を作る。手動で維持する配列は存在しない。

- ループの対象も、`config('apps.disabled')`/`config('app.disabled_apps')`による除外も、**同じ一覧から計算される**ので、「一覧には書いたが実体がない」という食い違いはそもそも起こり得ない(過去に一度、手動配列とファイルの食い違いで全サブドメインが500になった事故があった — ファイルシステムを単一情報源にすることで解消済み)
- ファイルが存在しないアプリ名へのパスは、そのパスだけ404になる(他アプリは無関係に動き続ける)

## 特定アプリを非公開にする(キルスイッチ)

`routes/apps/{name}.php` を削除・リネームせずに、そのパスだけを404にできる。方法は2通りで、`routes/web.php`のglob結果からどちらも合わせて差し引かれる。

### 通常の方法: `config/apps.php`(gitで管理・push不要でSSHもしない)

[config/apps.php](../config/apps.php)の`disabled`配列にアプリ名を追加して、普段どおりコミット・pushするだけ。

```php
'disabled' => ['post'],
```

既存のデプロイフロー(GitHub Actions → ConoHa WING)がそのまま`config:cache`し直してくれるので、SSHは不要。

### 緊急用: `.env`の`DISABLED_APPS`(SSHのみ、デプロイを待てない時用)

```
DISABLED_APPS=post   # .env に設定(カンマ区切りで複数可)
php artisan config:cache
```

pushや通常デプロイを待たずに即座に切り替えたい場合のみ使う。復旧は`.env`の行を消して`config:cache`し直す。

どちらの方法も、ファイル自体は残るので後で戻すのは簡単。他アプリには一切影響しない。

## 新しいミニアプリの追加方法

1. `routes/apps/{name}.php` を作成し、通常どおり `Route::get('/', ...)` 等を書く(このファイル内のパスは、そのアプリのprefixからの相対パスでよい。`Route::prefix()`は`routes/web.php`側で自動的に付与される)
2. コントローラー・ビュー・(必要なら)モデル/マイグレーションを追加する
3. コミット・pushする(**手順はこれだけ**。ホワイトリストを手動で書き換える必要はない — `routes/web.php`が`routes/apps/`ディレクトリを自動でスキャンする)

`root`という名前は特別扱い(apex、prefixなし)。それ以外の名前は自動的に`ruu-dev.com/{name}`になる。

## Inertiaページのタイトル/favicon

`resources/views/app.blade.php`(全Inertiaアプリ共通のroot view)は`root`アプリの既定値だけを持つ。個別のアプリはそれぞれの`Pages/{App}/*.tsx`側でタイトル/faviconを自分で上書きする(例: [Techpulse/Index.tsx](../resources/js/Pages/Techpulse/Index.tsx))。以前はapp.blade.php側にアプリ名をキーにした配列を置いて出し分けていたが、新しいInertiaアプリを追加するたびにこの配列を編集する必要があり、しかも「アプリ単位」の既定値しか表現できなかった(同じアプリ内のページごとに変えたい場合に対応できない)。各ページが自分のmetaを持つ形にしたことで、app.blade.phpは触らずに済むようになった。

タイトルとfaviconで実装方法が違う点に注意: `<Head title="...">`は動くが(Inertiaが`data-inertia`属性のない`<title>`を自動的に消してくれる、[@inertiajs/core](../node_modules/@inertiajs/core)のHeadManager挙動)、`<link rel="icon">`は同じようには消えない(Inertiaがtitle以外のタグを自動で入れ替えてくれるのはSSR使用時だけで、このプロジェクトはSSRを使っていない)。そのため favicon は [use-favicon.ts](../resources/js/lib/use-favicon.ts) フックで、`app.blade.php`側の`#app-favicon`要素の`href`を直接書き換える方式にしている。

## セッション/Cookie

全アプリが単一ドメイン(`ruu-dev.com`)配下のパスとして動くため、Laravelのデフォルトのセッションcookieが自然に全アプリで共有される。これは意図的な設計判断(移行時に決定)であり、アプリごとの分離は実装していない。`techpulse`/`zundamon`は元々ログインハブ(`login`アプリ)とセッションを共有する設計だったため、この変更後もそのまま動作する。

## ログインハブ(`login`アプリ)

`routes/apps/login.php`(`ruu-dev.com/login`)が、`techpulse`/`zundamon`向けの共有Google-loginハブ。許可されたGoogleアカウントの一覧は`config/restricted_apps.php`の`allowed_emails`。OAuthのリダイレクト/コールバックURLは常にこのアプリ固定([GoogleAuthController](../app/Http/Controllers/GoogleAuthController.php)が`route('login.home')`/`route('login.auth.google.callback')`と直接書いている)。パス移行時に`laravel`という名前(Laravelフレームワークと紛らわしい)から`login`に改名した。

## ローカルでの確認方法

- `http://root.localhost/{app}`(例: `http://root.localhost/memo`)にアクセスする。本番と同じ「単一ドメイン+パス」の構造をローカルでもそのまま再現しているだけなので、特別な設定は不要
- apexアプリ(`root`)は `http://root.localhost/`

---

## サブドメイン方式からの移行(2026-08)

### 移行前の構成(参考・履歴)

- DNS: `*.ruu-dev.com` のワイルドカードで、すべて同じConoHa WINGのオリジンを向いていた
- 手前のCloudflare Workerが、オリジンへ転送する際に`Host`を`laravel.ruu-dev.com`に書き換え、実際にアクセスされた元のサブドメイン名を`X-App-Subdomain`ヘッダーに入れて渡していた
- `App\Http\Middleware\ResolveAppSubdomain`がヘッダー/Hostからアプリ名を判定し、受信リクエストのパスの先頭に`/{app}`を足してからルーティングしていた(この付け替えはサーバー内部だけのもので、ブラウザ側URLには登場しなかった)
- `route()`/`action()`がこの内部プレフィックス込みのURLを返してしまう問題を`App\Routing\SubdomainAwareUrlGenerator`で、Inertiaの`url`ページpropの同種の問題を`HandleInertiaRequests::urlResolver()`で、それぞれ個別に補正していた

### 移行の目的

- **SEO/ドメイン評価の集約**: サブドメインは検索エンジンから見ると別ドメイン寄りに扱われがちで、被リンクやドメインオーソリティが分散する。単一ドメイン配下のパスに統一することでこれを集約する
- **管理コストの低減**: サブドメインごとに分散していたアナリティクス等の設定を単純化する

### 移行で変わったこと(2段階)

第1段階(パスを公開URLにする):

- `ResolveAppSubdomain`ミドルウェアを`ResolveAppFromPath`にリネーム・簡素化。ヘッダー/Host判定とリクエストの付け替えを廃止し、パスの第1セグメントを読んで`app_subdomain`属性をセットするだけにした
- `App\Routing\SubdomainAwareUrlGenerator`を削除(プレフィックスを剥がす処理が不要になった — むしろプレフィックスが残っているのが正しい公開URLなので、デフォルトの`UrlGenerator`をそのまま使う)
- `HandleInertiaRequests::urlResolver()`のオーバーライドを削除(同じ理由)
- 全アプリのセッション/Cookieを分離せず共有する方針にした(単一ドメインになったことで自動的にそうなる。以前存在した`config/restricted_apps.php`の`session_shared_apps`によるCookieドメイン拡張ハックは不要になり削除した)

第2段階(ミドルウェアそのものを削除):

- Google OAuthのURLは、実際には常に`login`アプリ(旧`laravel`アプリ、[下記参照](#ログインハブloginアプリ))固定だったと分かったので`GoogleAuthController`に直接`route('login.xxx')`と書くよう変更
- Inertiaのタイトル/favicon選択も、[上記](#inertiaページのタイトルfavicon)の通り各ページの`<Head>`に移した
- 上記2つが`app_subdomain`属性(`Request::appSubdomain()`マクロ)の最後の利用箇所だったため、`ResolveAppFromPath`ミドルウェアと`appSubdomain()`マクロを完全に削除。ミドルウェアが持っていたアプリ一覧の取得ロジック(`routes/apps/*.php`のglob + disabled除外)は`routes/web.php`に直接インライン化した
- ログインハブアプリを`laravel`から`login`に改名(`routes/apps/laravel.php` → `routes/apps/login.php`、`resources/views/laravel/` → `resources/views/login/`、ルート名`laravel.*` → `login.*`)。「laravel」という名前がLaravelフレームワーク自体と紛らわしく、ミドルウェア削除でOAuthのURLを直書きするタイミングで気づいたため

### 意図的に対応しなかったこと

- **旧サブドメインURLへの301リダイレクト**: 実装していない。`memo.ruu-dev.com`のような旧URLは、Cloudflare Worker/DNSの設定自体は残っているものの、Laravel側がもうヘッダー/Hostベースの振り分けをしないため、多くの場合意図した内容を返さない(パスが`/`のリクエストとして扱われ、`root`アプリ向けにフォールバックする)。将来的に旧サブドメインを完全に切り離す前提で、今回はあえて何もしていない
- **Cloudflare Worker / DNSの変更**: 一切行っていない。新しいパスベースのアクセス(`ruu-dev.com/{app}/...`)はapex DNSに直接届き、Workerを経由しないため、Worker側の変更なしで移行が完結した

### 移行時にユーザー側で対応が必要だった項目

- Google Cloud Consoleの OAuth 認可済みリダイレクトURIを `https://ruu-dev.com/login/auth/google/callback` に更新(旧`laravel.ruu-dev.com`ベースのURL、あるいは第1段階時点の`https://ruu-dev.com/laravel/auth/google/callback`のままだと本番ログインが壊れる)
