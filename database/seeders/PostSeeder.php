<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::create([
            "title" =>
                "ラズパイ3台でエヴァのMAGIを作る｜ローカルLLMで動く合議AIシステム  SIOS Tech Lab",
            "content" =>
                "「新世紀エヴァンゲリオン」のお気に入りで、ローカルLLM（Ollama）とRaspberry  Piで作られた意思決定コンピュータMAGIを紹介しました。3台のPiがそれぞれ独立し た人格になり、それぞれ異なる意見を出して多数決で結論を出す合議AIシステムです 。1台のPiは推論だけ、他の2台は投票と理由の提供のみ。ロールベースに分担し、コ ンテキスト長・生成トークン数・モデル常駐の3点で速度と安定性を調整しました。 「アイス食べたい」をテーマにした討議を実装し、実用的な合議AIを構築しました。 また、エヴァンゲリオンファンを対象に、「意見が割れる議題」で試す検証結果も公 開しています。",
        ]);
        Post::create([
            "title" =>
                "現役Appleマップエンジニアが書いた日本の住所表記のやばさを指摘する「ヤバい日本の住所」が出版",
            "content" =>
                "現役Appleマップエンジニアの河合太郎氏が著書「ヤバい日本の住所」を出版。日本 の住所表記は曖昧で地図との紐付けが難しいという問題を指摘し、AI技術での解決も 視野に入れつつ解説。元になっているのは2023年6月7日noteに公開された「とにかく 日本の住所のヤバさをもっと知るべきだと思います」で、朝日新聞の「多様すぎる日 本の住所」として紹介され話題を呼んだ。本書はその著作を基に執筆された。",
        ]);
    }
}
