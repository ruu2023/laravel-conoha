<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- root app's defaults. Title: Inertia's <Head title="..."> replaces
             any <title> lacking its own data-inertia marker, so pages that
             set one automatically override this. Favicon: Inertia only
             dedupes <title> that way, not arbitrary tags (would just be
             appended alongside this one, not replace it), so pages that want
             a different favicon swap this element's href directly instead
             (see resources/js/lib/use-favicon.ts). --}}
        <title inertia>100 Days of Code</title>
        <link rel="icon" id="app-favicon" href="{{ asset('favicon.ico') }}" sizes="any">
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx'])
        @inertiaHead
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-SWCLP4WEZ5"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-SWCLP4WEZ5', { send_page_view: false }); // 自動送信をOFF
        </script>
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
