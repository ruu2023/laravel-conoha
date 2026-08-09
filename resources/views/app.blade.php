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
             (see resources/js/lib/use-favicon.ts). These OG/meta tags are
             shared across every Inertia page (root LP, techpulse, zundamon);
             gated pages add their own noindex via <Head> instead of
             overriding these (see Pages/Techpulse, Pages/Zundamon). --}}
        <title inertia>100日間のアプリ開発｜100 Days Challenge</title>
        <link rel="icon" id="app-favicon" href="{{ asset('favicon.ico') }}" sizes="any">
        @php
            $seoDescription = '個人開発者が100日間で100個のアプリを作る「100 Days Challenge」。実際に使ってほしいアプリを公開しています。';
            $seoUrl = url()->current();
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ $seoUrl }}">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="100 Days Challenge">
        <meta property="og:title" content="100日間のアプリ開発｜100 Days Challenge">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">
        <meta property="og:image" content="{{ asset('about-illustration.png') }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@ruu_web">
        <meta name="twitter:title" content="100日間のアプリ開発｜100 Days Challenge">
        <meta name="twitter:description" content="{{ $seoDescription }}">
        <meta name="twitter:image" content="{{ asset('about-illustration.png') }}">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "WebSite",
            "name": "100 Days Challenge",
            "url": "{{ url('/') }}",
            "description": "{{ $seoDescription }}",
            "inLanguage": "ja"
        }
        </script>
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
