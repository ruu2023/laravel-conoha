<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ログイン</title>
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
            a.login-button {
                display: inline-block;
                margin-top: 1.5rem;
                padding: 0.75rem 1.5rem;
                border-radius: 0.5rem;
                background: #4285F4;
                color: #fff;
                text-decoration: none;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div>
            <h1>限定公開ミニアプリ</h1>
            <p>許可されたGoogleアカウントのみ利用できます。</p>
            <a class="login-button" href="{{ route('login.auth.google.redirect') }}">Googleでログイン</a>
        </div>
    </body>
</html>
