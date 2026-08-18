<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-KH6L17GQP1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-KH6L17GQP1');
        </script>

        <title>テキスト統計アプリ | 文字数・行数・読了時間をリアルタイム計測</title>
        @php
            $seoDescription = '文章を入力するだけで文字数・空白なし文字数・行数・単語数・読了時間をリアルタイムに計算する無料のテキスト統計ツール。';
            $seoUrl = url('/textstats');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <meta name="google-site-verification" content="CmL0cYObXLPrUATx_ToU_m4CmOPgTX1rgsKvnIe7aAs" />
        <link rel="canonical" href="{{ $seoUrl }}">
        <meta name="theme-color" content="#8b5cf6">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="テキスト統計アプリ">
        <meta property="og:title" content="テキスト統計アプリ | 文字数・行数・読了時間をリアルタイム計測">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="テキスト統計アプリ | 文字数・行数・読了時間をリアルタイム計測">
        <meta name="twitter:description" content="{{ $seoDescription }}">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "テキスト統計アプリ",
            "url": "{{ $seoUrl }}",
            "description": "{{ $seoDescription }}",
            "applicationCategory": "UtilitiesApplication",
            "operatingSystem": "Any",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "JPY"
            },
            "inLanguage": "ja"
        }
        </script>

        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                background: #030712;
                color: #fff;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Hiragino Sans", "Yu Gothic", sans-serif;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 1.5rem;
            }
            h1 {
                font-size: 1.875rem;
                font-weight: 700;
                letter-spacing: -0.025em;
                margin: 0;
            }
            .subtitle {
                color: #6b7280;
                font-size: 0.875rem;
                margin: 0.25rem 0 1.5rem;
            }
            .stats {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 0.75rem;
                width: 100%;
                max-width: 42rem;
                margin-bottom: 1.5rem;
            }
            .stat-card {
                background: #1f2937;
                border-radius: 0.5rem;
                padding: 0.75rem;
                text-align: center;
            }
            .stat-value {
                color: #a78bfa;
                font-size: 1.5rem;
                font-weight: 700;
            }
            .stat-unit {
                font-size: 0.875rem;
                color: #6b7280;
            }
            .stat-label {
                font-size: 0.875rem;
                color: #9ca3af;
            }
            .editor {
                width: 100%;
                max-width: 42rem;
            }
            textarea {
                width: 100%;
                height: 16rem;
                border-radius: 0.75rem;
                background: #1f2937;
                color: #f3f4f6;
                border: 1px solid #374151;
                padding: 1rem;
                font-size: 0.875rem;
                line-height: 1.625;
                resize: none;
                outline: none;
                transition: border-color 0.2s;
                font-family: inherit;
            }
            textarea:focus {
                border-color: #8b5cf6;
            }
            .actions {
                display: flex;
                justify-content: flex-end;
                margin-top: 0.5rem;
            }
            .clear-btn {
                background: none;
                border: none;
                cursor: pointer;
                font-size: 0.75rem;
                color: #6b7280;
                transition: color 0.2s;
            }
            .clear-btn:hover {
                color: #a78bfa;
            }
            @media (max-width: 640px) {
                .stats { grid-template-columns: repeat(2, 1fr); }
            }
        </style>
    </head>
    <body>
        <h1>テキスト統計アプリ</h1>
        <p class="subtitle">文章を入力してください - リアルタイムで統計値が更新されます</p>

        <div class="stats" id="stats">
            <div class="stat-card">
                <div class="stat-value" id="stat-totalChars">0</div>
                <div class="stat-unit">文字</div>
                <div class="stat-label">文字数</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-charsNoSpace">0</div>
                <div class="stat-unit">文字</div>
                <div class="stat-label">空白なし</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-lines">0</div>
                <div class="stat-unit">行</div>
                <div class="stat-label">行数</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-words">0</div>
                <div class="stat-unit">語</div>
                <div class="stat-label">単語数</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="stat-readingMinutes">0</div>
                <div class="stat-unit">分</div>
                <div class="stat-label">読了時間</div>
            </div>
        </div>

        <div class="editor">
            <textarea id="text" placeholder=""></textarea>
            <div class="actions">
                <button class="clear-btn" id="clearBtn">クリア</button>
            </div>
        </div>

        <script>
            const JAPANESE_CHRS_PER_MINUTE = 400;

            function calcStats(text) {
                const totalChars = text.length;
                const charsNoSpace = text.replace(/\s/g, "").length;
                const lines = text === "" ? 0 : text.split("\n").length;
                const words = text.trim() === "" ? 0 : text.trim().split(/\s+/).length;
                const readingMinutes = charsNoSpace === 0
                    ? 0
                    : Math.max(1, Math.round(charsNoSpace / JAPANESE_CHRS_PER_MINUTE));
                return { totalChars, charsNoSpace, lines, words, readingMinutes };
            }

            const textarea = document.getElementById('text');
            const clearBtn = document.getElementById('clearBtn');

            function render() {
                const stats = calcStats(textarea.value);
                for (const key in stats) {
                    document.getElementById('stat-' + key).textContent = stats[key];
                }
            }

            textarea.addEventListener('input', render);
            clearBtn.addEventListener('click', () => {
                textarea.value = '';
                render();
            });

            render();
        </script>
    </body>
</html>
