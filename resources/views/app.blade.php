<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php
            // Inertiaのroot viewはアプリ間で共通なので、サブドメイン毎の既定値
            // (タイトル/favicon)をここで出し分ける。各ページはInertiaの<Head>で
            // さらに上書きできる(title inertia属性がある要素はInertiaが差し替える)。
            // faviconはruu-dev(root)のアイコンをアプリ共通の既定値とし、専用の
            // アイコンを用意したアプリだけここで上書きする。
            $inertiaDefaults = [
                'root' => ['title' => '100 Days of Code'],
                'techpulse' => ['title' => 'AI Tech Pulse', 'favicon' => 'techpulse-favicon.png'],
                'zundamon' => ['title' => 'ずんだもんNEWS', 'favicon' => 'zundamon-favicon.png'],
            ];
            $inertiaMeta = $inertiaDefaults[request()->appSubdomain()] ?? [];
            $inertiaTitle = $inertiaMeta['title'] ?? 'ruu-dev.com';
            $inertiaFavicon = $inertiaMeta['favicon'] ?? 'favicon.ico';
        @endphp
        <title inertia>{{ $inertiaTitle }}</title>
        <link rel="icon" href="{{ asset($inertiaFavicon) }}" sizes="any">
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx'])
        @inertiaHead
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
