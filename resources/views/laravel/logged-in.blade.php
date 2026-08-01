<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ログイン済み</title>
        <meta name="robots" content="noindex">
        <style>
            :root { color-scheme: light dark; }
            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                text-align: center;
            }
            ul { list-style: none; padding: 0; }
        </style>
    </head>
    <body>
        <div>
            <h1>ログイン済みです</h1>
            <p>{{ $email }}</p>
            <ul>
                @foreach ($appUrls as $app => $url)
                    <li><a href="{{ $url }}">{{ $app }}</a></li>
                @endforeach
            </ul>
        </div>
    </body>
</html>
