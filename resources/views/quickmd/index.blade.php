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

        <title>Quick Markdown | ライブプレビュー付きMarkdownメモ</title>
        @php
            $seoDescription = '入力したMarkdownをリアルタイムでプレビューしながら自動保存できる、ブラウザ完結の軽量Markdownメモアプリ。';
            $seoUrl = url('/quickmd');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <meta name="google-site-verification" content="CmL0cYObXLPrUATx_ToU_m4CmOPgTX1rgsKvnIe7aAs" />
        <link rel="canonical" href="{{ $seoUrl }}">
        <meta name="theme-color" content="#3b82f6">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="Quick Markdown">
        <meta property="og:title" content="Quick Markdown | ライブプレビュー付きMarkdownメモ">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Quick Markdown | ライブプレビュー付きMarkdownメモ">
        <meta name="twitter:description" content="{{ $seoDescription }}">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "Quick Markdown",
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
                background: #f8fafc;
                color: #1e293b;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Hiragino Sans", "Yu Gothic", sans-serif;
            }
            .wrap {
                max-width: 64rem;
                margin: 0 auto;
            }
            header {
                background: #fff;
                border-bottom: 1px solid #e2e8f0;
                padding: 1rem 1.5rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 10;
            }
            .brand {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .brand-icon {
                background: #3b82f6;
                padding: 0.5rem;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(59,130,246,0.2);
                display: flex;
            }
            .brand-icon svg { fill: #fff; }
            .brand h1 {
                font-weight: 700;
                font-size: 1.125rem;
                margin: 0;
            }
            .clear-btn {
                background: none;
                border: none;
                cursor: pointer;
                padding: 0.5rem;
                color: #64748b;
                transition: color 0.2s;
                font-size: 0.875rem;
            }
            .clear-btn:hover { color: #f87171; }
            .grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1rem;
                margin-top: 0.75rem;
                padding: 0 1rem 1.5rem;
            }
            @media (min-width: 768px) {
                .grid { grid-template-columns: 1fr 1fr; }
            }
            .panel-label {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5rem;
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                color: #94a3b8;
            }
            textarea {
                width: 100%;
                height: 70vh;
                padding: 1.5rem;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 0.75rem;
                box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                outline: none;
                resize: none;
                font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
                font-size: 0.875rem;
                line-height: 1.625;
            }
            textarea:focus {
                border-color: transparent;
                box-shadow: 0 0 0 2px #3b82f6;
            }
            #preview {
                width: 100%;
                height: 70vh;
                padding: 1.5rem;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 1rem;
                overflow-y: auto;
                box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08);
            }
            #preview h1 { font-size: 1.875rem; font-weight: 700; margin: 0 0 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem; }
            #preview h2 { font-size: 1.5rem; font-weight: 600; margin: 1.5rem 0 0.75rem; }
            #preview h3 { font-size: 1.25rem; font-weight: 500; margin: 1rem 0 0.5rem; }
            #preview li { margin-left: 1rem; list-style: disc; }
            .mobile-toggle {
                display: none;
                gap: 0.5rem;
                padding: 0 1rem;
            }
            @media (max-width: 767px) {
                .mobile-toggle { display: flex; }
                .grid.editing #preview-section { display: none; }
                .grid:not(.editing) #editor-section { display: none; }
            }
            .toggle-btn {
                flex: 1;
                padding: 0.5rem;
                border-radius: 0.5rem;
                border: 1px solid #e2e8f0;
                background: #fff;
                cursor: pointer;
                font-size: 0.875rem;
            }
            .toggle-btn.active {
                background: #3b82f6;
                color: #fff;
                border-color: #3b82f6;
            }
        </style>
    </head>
    <body>
        <div class="wrap">
            <header>
                <div class="brand">
                    <div class="brand-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24"><path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z"/></svg>
                    </div>
                    <h1>Quick Markdown</h1>
                </div>
                <button class="clear-btn" id="clearBtn">クリア</button>
            </header>

            <div class="mobile-toggle">
                <button class="toggle-btn active" id="editToggle">Editor</button>
                <button class="toggle-btn" id="previewToggle">Preview</button>
            </div>

            <div class="grid editing" id="grid">
                <section id="editor-section">
                    <div class="panel-label">
                        <span>Markdown Editor</span>
                        <span>Auto-saving</span>
                    </div>
                    <textarea id="markdown" placeholder="Start typing..." autofocus></textarea>
                </section>

                <div id="preview-section">
                    <div class="panel-label"><span>Preview</span></div>
                    <div id="preview"></div>
                </div>
            </div>
        </div>

        <script>
            const STORAGE_KEY = 'quick-memo-v2';
            const textarea = document.getElementById('markdown');
            const preview = document.getElementById('preview');
            const grid = document.getElementById('grid');
            const editToggle = document.getElementById('editToggle');
            const previewToggle = document.getElementById('previewToggle');
            let saveTimer = null;

            function parseMarkdown(text) {
                return text
                    .replace(/^# (.*$)/gim, '<h1>$1</h1>')
                    .replace(/^## (.*$)/gim, '<h2>$1</h2>')
                    .replace(/^### (.*$)/gim, '<h3>$1</h3>')
                    .replace(/\*\*(.*)\*\*/gim, '<strong>$1</strong>')
                    .replace(/\*(.*)\*/gim, '<em>$1</em>')
                    .replace(/^- (.*$)/gim, '<li>$1</li>')
                    .replace(/\n/gim, '<br />');
            }

            function render() {
                preview.innerHTML = parseMarkdown(textarea.value);
            }

            textarea.addEventListener('input', () => {
                render();
                clearTimeout(saveTimer);
                saveTimer = setTimeout(() => {
                    localStorage.setItem(STORAGE_KEY, textarea.value);
                }, 500);
            });

            document.getElementById('clearBtn').addEventListener('click', () => {
                if (confirm('Clear?')) {
                    textarea.value = '';
                    render();
                    localStorage.setItem(STORAGE_KEY, '');
                }
            });

            editToggle.addEventListener('click', () => {
                grid.classList.add('editing');
                editToggle.classList.add('active');
                previewToggle.classList.remove('active');
            });
            previewToggle.addEventListener('click', () => {
                grid.classList.remove('editing');
                previewToggle.classList.add('active');
                editToggle.classList.remove('active');
            });

            const saved = localStorage.getItem(STORAGE_KEY);
            textarea.value = saved !== null ? saved : '# Hello World\n- **太字**も使えます\n- 30分で完成させよう';
            render();
        </script>
    </body>
</html>
