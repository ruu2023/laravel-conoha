<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Universal Draft App | 文字数制限つき自動保存メモ</title>
        @php
            $seoDescription = '文字数制限を自由に設定できる、ブラウザ自動保存メモアプリ。X・Instagram・小論文・レポート・原稿用紙などのプリセットに対応し、文字数超過を自動でアラート。';
            $seoUrl = url('/');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <meta name="google-site-verification" content="CmL0cYObXLPrUATx_ToU_m4CmOPgTX1rgsKvnIe7aAs" />
        <link rel="canonical" href="{{ $seoUrl }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}">
        <meta name="theme-color" content="#eef1ec">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="Universal Draft App">
        <meta property="og:title" content="Universal Draft App | 文字数制限つき自動保存メモ">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Universal Draft App | 文字数制限つき自動保存メモ">
        <meta name="twitter:description" content="{{ $seoDescription }}">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "Universal Draft App",
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

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@500;700&family=Zen+Kaku+Gothic+New:wght@400;500;700&display=swap" rel="stylesheet">

        <style>
            :root {
                color-scheme: light dark;
                --paper: #eef1ec;
                --surface: #fbfbf7;
                --surface-2: #e9e6dd;
                --border: #dcd8cc;
                --ink: #201f1c;
                --ink-muted: #726f64;
                --accent: #b8262c;
                --accent-soft: rgba(184, 38, 44, 0.14);
                --accent-contrast: #fdf6ee;
                --danger: #b8262c;
                --danger-bg: #f7e6e2;
                --radius-lg: 18px;
                --radius-md: 12px;
                --radius-sm: 8px;
                --shadow: 0 1px 2px rgba(32, 28, 20, 0.05), 0 16px 36px -18px rgba(32, 28, 20, 0.28);
                --font-display: 'Shippori Mincho', serif;
                --font-body: 'Zen Kaku Gothic New', 'Hiragino Sans', 'Noto Sans JP', sans-serif;
                --font-mono: ui-monospace, 'SFMono-Regular', Menlo, Consolas, monospace;
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    --paper: #171511;
                    --surface: #211e19;
                    --surface-2: #2b2721;
                    --border: #3a352c;
                    --ink: #f1ede3;
                    --ink-muted: #a29c8c;
                    --accent: #ff6b52;
                    --accent-soft: rgba(255, 107, 82, 0.2);
                    --accent-contrast: #201007;
                    --danger: #ff6b52;
                    --danger-bg: #3a1c15;
                    --shadow: 0 1px 2px rgba(0, 0, 0, 0.3), 0 16px 36px -18px rgba(0, 0, 0, 0.6);
                }
            }

            * {
                box-sizing: border-box;
            }

            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                background: var(--paper);
                color: var(--ink);
                font-family: var(--font-body);
                -webkit-font-smoothing: antialiased;
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: 100vh;
                padding: 28px 16px 56px;
            }

            .app {
                width: 100%;
                max-width: 640px;
            }

            .top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 20px;
            }

            .brand {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .hanko {
                flex-shrink: 0;
                width: 42px;
                height: 42px;
                border-radius: 50%;
                background: var(--accent);
                color: var(--accent-contrast);
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: var(--font-display);
                font-size: 19px;
                font-weight: 700;
                transform: rotate(-6deg);
                box-shadow: 0 0 0 1.5px rgba(184, 38, 44, 0.3), 0 6px 14px -6px rgba(184, 38, 44, 0.55);
                user-select: none;
            }

            .titles h1 {
                margin: 0 0 2px;
                font-family: var(--font-display);
                font-size: 23px;
                font-weight: 700;
                letter-spacing: 0.01em;
            }

            .titles p {
                margin: 0;
                font-size: 12.5px;
                color: var(--ink-muted);
            }

            .icon-btn {
                flex-shrink: 0;
                width: 40px;
                height: 40px;
                border-radius: 12px;
                border: 1px solid var(--border);
                background: var(--surface);
                color: var(--ink);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: border-color 0.15s ease, transform 0.15s ease;
            }

            .icon-btn:hover {
                border-color: var(--accent);
            }

            .icon-btn:active {
                transform: scale(0.94);
            }

            .icon-btn svg {
                width: 20px;
                height: 20px;
            }

            .card {
                background: var(--surface);
                border: 1px solid var(--border);
                border-radius: var(--radius-lg);
                box-shadow: var(--shadow);
                padding: 18px;
            }

            .preset-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                font-weight: 600;
                color: var(--ink-muted);
                background: var(--surface-2);
                border: 1px solid var(--border);
                border-radius: 999px;
                padding: 5px 12px;
                margin-bottom: 12px;
            }

            .preset-chip .dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: var(--accent);
            }

            .editor {
                position: relative;
            }

            .hl-backdrop {
                position: absolute;
                inset: 0;
                overflow: hidden;
                pointer-events: none;
                border-radius: 6px;
            }

            .hl-inner {
                white-space: pre-wrap;
                word-wrap: break-word;
                font: inherit;
                line-height: 1.7;
                font-size: 16px;
                color: transparent;
                padding: 0;
                margin: 0;
                will-change: transform;
            }

            .hl-inner mark {
                background: var(--accent-soft);
                color: transparent;
                border-radius: 2px;
                box-decoration-break: clone;
                -webkit-box-decoration-break: clone;
            }

            textarea#draft {
                position: relative;
                width: 100%;
                min-height: 320px;
                resize: vertical;
                border: none;
                outline: none;
                background: transparent;
                color: var(--ink);
                caret-color: var(--accent);
                font-size: 16px;
                line-height: 1.7;
                font-family: inherit;
            }

            textarea#draft::placeholder {
                color: var(--ink-muted);
            }

            .meter-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-top: 14px;
                padding-top: 14px;
                border-top: 1px solid var(--border);
            }

            .count-text {
                font-family: var(--font-mono);
                font-size: 13px;
                font-weight: 600;
                color: var(--ink-muted);
                white-space: nowrap;
                font-variant-numeric: tabular-nums;
            }

            .count-text .current {
                color: var(--ink);
                font-size: 15px;
            }

            .count-text.over .current {
                color: var(--danger);
            }

            .bar-track {
                flex: 1;
                height: 6px;
                border-radius: 999px;
                background: var(--surface-2);
                overflow: hidden;
            }

            .bar-fill {
                height: 100%;
                border-radius: 999px;
                background: var(--accent);
                width: 0%;
                transition: width 0.15s ease, background-color 0.15s ease;
            }

            .alert {
                display: none;
                align-items: center;
                gap: 8px;
                margin-top: 12px;
                padding: 10px 12px;
                border-radius: var(--radius-sm);
                background: var(--danger-bg);
                color: var(--danger);
                font-size: 13px;
                font-weight: 600;
            }

            .alert.show {
                display: flex;
            }

            .alert svg {
                width: 16px;
                height: 16px;
                flex-shrink: 0;
            }

            .actions {
                display: flex;
                gap: 10px;
                margin-top: 16px;
            }

            .btn {
                flex: 1;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 12px 16px;
                border-radius: var(--radius-md);
                border: 1px solid transparent;
                font-family: var(--font-body);
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: opacity 0.15s ease, transform 0.15s ease;
            }

            .btn svg {
                width: 16px;
                height: 16px;
            }

            .btn:active {
                transform: scale(0.98);
            }

            .btn-primary {
                background: var(--ink);
                color: var(--paper);
            }

            @media (prefers-color-scheme: dark) {
                .btn-primary {
                    background: #f1ede3;
                    color: #201007;
                }
            }

            .btn-primary:hover {
                opacity: 0.88;
            }

            .btn-ghost {
                background: var(--surface);
                border-color: var(--border);
                color: var(--ink);
                flex: none;
                width: 44px;
            }

            .btn-ghost:hover {
                border-color: var(--accent);
            }

            .save-status {
                text-align: center;
                font-size: 12px;
                color: var(--ink-muted);
                margin-top: 12px;
                min-height: 16px;
                opacity: 0;
                transition: opacity 0.2s ease;
            }

            .save-status.show {
                opacity: 1;
            }

            /* Extract panel */
            .extract {
                margin-top: 18px;
            }

            .section-label {
                display: block;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: var(--accent);
                margin-bottom: 14px;
            }

            .extract-controls {
                display: flex;
                gap: 10px;
                margin-bottom: 14px;
            }

            .extract-field {
                flex: 1;
            }

            .extract-field label {
                display: block;
                font-size: 11.5px;
                color: var(--ink-muted);
                margin-bottom: 6px;
            }

            .extract-field label .code {
                font-family: var(--font-mono);
                font-size: 10.5px;
                opacity: 0.8;
            }

            .extract-field input {
                width: 100%;
                padding: 10px 12px;
                border-radius: var(--radius-sm);
                border: 1px solid var(--border);
                background: var(--paper);
                color: var(--ink);
                font-family: var(--font-mono);
                font-size: 15px;
                font-weight: 600;
                outline: none;
            }

            .extract-field input:focus {
                border-color: var(--accent);
            }

            .extract-result {
                position: relative;
                background: var(--paper);
                border: 1px dashed var(--border);
                border-radius: var(--radius-sm);
                padding: 16px 34px;
                min-height: 44px;
                font-family: var(--font-display);
                font-size: 16px;
                line-height: 1.6;
                color: var(--ink);
                word-break: break-word;
            }

            .extract-result.empty {
                color: var(--ink-muted);
                font-family: var(--font-body);
                font-size: 13px;
            }

            .extract-result .quote-mark {
                position: absolute;
                font-family: var(--font-display);
                font-size: 28px;
                color: var(--accent-soft);
                line-height: 1;
            }

            .extract-result .quote-open {
                top: 8px;
                left: 10px;
            }

            .extract-result .quote-close {
                bottom: 2px;
                right: 10px;
            }

            .extract-meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                margin-top: 10px;
            }

            .extract-meta span {
                font-family: var(--font-mono);
                font-size: 12px;
                color: var(--ink-muted);
            }

            .mini-btn {
                border: 1px solid var(--border);
                background: var(--surface);
                color: var(--ink);
                font-family: var(--font-body);
                font-size: 12.5px;
                font-weight: 600;
                padding: 7px 14px;
                border-radius: 999px;
                cursor: pointer;
                transition: border-color 0.15s ease, transform 0.15s ease;
            }

            .mini-btn:hover {
                border-color: var(--accent);
            }

            .mini-btn:active {
                transform: scale(0.96);
            }

            /* Modal */
            .overlay {
                position: fixed;
                inset: 0;
                background: rgba(20, 18, 14, 0.45);
                display: none;
                align-items: flex-end;
                justify-content: center;
                padding: 0;
                z-index: 50;
            }

            .overlay.show {
                display: flex;
            }

            @media (min-width: 640px) {
                .overlay {
                    align-items: center;
                    padding: 24px;
                }
            }

            .modal {
                width: 100%;
                max-width: 420px;
                background: var(--surface);
                border-radius: 24px 24px 0 0;
                padding: 24px 20px calc(20px + env(safe-area-inset-bottom, 0px));
                box-shadow: var(--shadow);
                transform: translateY(16px);
                opacity: 0;
                transition: transform 0.2s ease, opacity 0.2s ease;
            }

            @media (min-width: 640px) {
                .modal {
                    border-radius: var(--radius-lg);
                    padding: 28px;
                }
            }

            .overlay.show .modal {
                transform: translateY(0);
                opacity: 1;
            }

            .modal h2 {
                margin: 0 0 18px;
                font-family: var(--font-display);
                font-size: 19px;
                font-weight: 700;
            }

            .preset-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-bottom: 20px;
            }

            .preset-btn {
                appearance: none;
                border: 1px solid var(--border);
                background: var(--surface);
                color: var(--ink);
                font-family: var(--font-body);
                font-size: 14px;
                font-weight: 600;
                padding: 13px 10px;
                border-radius: 999px;
                cursor: pointer;
                text-align: center;
                transition: all 0.12s ease;
            }

            .preset-btn:hover {
                border-color: var(--accent);
            }

            .preset-btn.active {
                background: var(--accent);
                border-color: var(--accent);
                color: var(--accent-contrast);
            }

            .field-label {
                display: block;
                font-size: 13px;
                color: var(--ink-muted);
                margin-bottom: 8px;
            }

            .custom-input {
                width: 100%;
                padding: 14px 16px;
                border-radius: var(--radius-md);
                border: 1px solid var(--border);
                background: var(--surface);
                color: var(--ink);
                font-family: var(--font-mono);
                font-size: 18px;
                font-weight: 700;
                outline: none;
                margin-bottom: 22px;
            }

            .custom-input:focus {
                border-color: var(--accent);
            }

            .modal-actions {
                display: flex;
                gap: 10px;
            }

            .btn-cancel {
                flex: 1;
                background: var(--surface);
                border: 1px solid var(--border);
                color: var(--ink);
                font-family: var(--font-body);
                padding: 14px;
                border-radius: var(--radius-md);
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
            }

            .btn-save {
                flex: 1;
                background: var(--accent);
                border: 1px solid var(--accent);
                color: var(--accent-contrast);
                font-family: var(--font-body);
                padding: 14px;
                border-radius: var(--radius-md);
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
            }

            .btn-cancel:hover, .btn-save:hover {
                opacity: 0.9;
            }

            .toast {
                position: fixed;
                bottom: 24px;
                left: 50%;
                transform: translateX(-50%) translateY(12px);
                background: var(--ink);
                color: var(--paper);
                padding: 10px 18px;
                border-radius: 999px;
                font-size: 13px;
                font-weight: 600;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease, transform 0.2s ease;
                z-index: 60;
            }

            .toast.show {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

            :focus-visible {
                outline: 2px solid var(--accent);
                outline-offset: 2px;
            }

            @media (prefers-reduced-motion: reduce) {
                * {
                    transition-duration: 0.001ms !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="app">
            <div class="top">
                <div class="brand">
                    <span class="hanko" aria-hidden="true">稿</span>
                    <div class="titles">
                        <h1>Universal Draft App</h1>
                        <p>文字数制限を自由に設定できる、自動保存メモ</p>
                    </div>
                </div>
                <button type="button" class="icon-btn" id="settingsBtn" aria-label="文字数設定を開く">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c.63.24 1.19.63 1.51 1.28.32.65.32 1.28 0 1.72"></path>
                    </svg>
                </button>
            </div>

            <div class="card">
                <span class="preset-chip"><span class="dot"></span><span id="presetLabel">原稿用紙1枚 (400)</span></span>

                <div class="editor">
                    <div class="hl-backdrop" id="hlBackdrop" aria-hidden="true"><div class="hl-inner" id="hlInner"></div></div>
                    <textarea id="draft" placeholder="ここに下書きを入力してください。入力内容はブラウザに自動保存されます。" spellcheck="false"></textarea>
                </div>

                <div class="meter-row">
                    <div class="bar-track">
                        <div class="bar-fill" id="barFill"></div>
                    </div>
                    <div class="count-text" id="countText"><span class="current">0</span> / 400</div>
                </div>

                <div class="alert" id="alertBox">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span id="alertText">文字数が上限を超えています</span>
                </div>

                <div class="actions">
                    <button type="button" class="btn btn-primary" id="copyBtn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        コピー
                    </button>
                    <button type="button" class="btn btn-ghost" id="clearBtn" aria-label="本文をクリア">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>

                <div class="save-status" id="saveStatus">保存済み</div>
            </div>

            <div class="card extract">
                <span class="section-label">範囲を抽出 ・ Extract range</span>

                <div class="extract-controls">
                    <div class="extract-field">
                        <label for="rangeStart">開始位置 <span class="code">startIndex</span></label>
                        <input type="number" id="rangeStart" min="0" value="0" inputmode="numeric">
                    </div>
                    <div class="extract-field">
                        <label for="rangeLength">文字数 <span class="code">length</span></label>
                        <input type="number" id="rangeLength" min="0" value="10" inputmode="numeric">
                    </div>
                </div>

                <div class="extract-result" id="extractResult">
                    <span class="quote-mark quote-open" aria-hidden="true">"</span>
                    <span id="extractText"></span>
                    <span class="quote-mark quote-close" aria-hidden="true">"</span>
                </div>

                <div class="extract-meta">
                    <span id="extractMeta">0〜9文字目 ・ 10文字</span>
                    <button type="button" class="mini-btn" id="extractCopyBtn">コピー</button>
                </div>
            </div>
        </div>

        <div class="overlay" id="overlay">
            <div class="modal">
                <h2>最大文字数を設定</h2>
                <div class="preset-grid" id="presetGrid"></div>
                <label class="field-label" for="customLimit">カスタム文字数</label>
                <input type="number" id="customLimit" class="custom-input" min="1" inputmode="numeric">
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="cancelBtn">キャンセル</button>
                    <button type="button" class="btn-save" id="saveBtn">保存</button>
                </div>
            </div>
        </div>

        <div class="toast" id="toast"></div>

        <script>
        (function () {
            var PRESETS = [
                { label: 'X (旧Twitter)', limit: 140 },
                { label: 'Instagram', limit: 2200 },
                { label: '小論文', limit: 800 },
                { label: 'レポート', limit: 2000 },
                { label: '原稿用紙1枚', limit: 400 },
            ];

            var STORAGE_TEXT = 'universalDraftApp:text';
            var STORAGE_LIMIT = 'universalDraftApp:limit';
            var STORAGE_LABEL = 'universalDraftApp:label';
            var STORAGE_RANGE_START = 'universalDraftApp:rangeStart';
            var STORAGE_RANGE_LENGTH = 'universalDraftApp:rangeLength';

            var draft = document.getElementById('draft');
            var countText = document.getElementById('countText');
            var barFill = document.getElementById('barFill');
            var alertBox = document.getElementById('alertBox');
            var presetLabelEl = document.getElementById('presetLabel');
            var saveStatus = document.getElementById('saveStatus');
            var toast = document.getElementById('toast');

            var settingsBtn = document.getElementById('settingsBtn');
            var overlay = document.getElementById('overlay');
            var presetGrid = document.getElementById('presetGrid');
            var customLimit = document.getElementById('customLimit');
            var cancelBtn = document.getElementById('cancelBtn');
            var saveBtn = document.getElementById('saveBtn');
            var copyBtn = document.getElementById('copyBtn');
            var clearBtn = document.getElementById('clearBtn');

            var hlInner = document.getElementById('hlInner');
            var rangeStart = document.getElementById('rangeStart');
            var rangeLength = document.getElementById('rangeLength');
            var extractText = document.getElementById('extractText');
            var extractResult = document.getElementById('extractResult');
            var extractMeta = document.getElementById('extractMeta');
            var extractCopyBtn = document.getElementById('extractCopyBtn');

            var state = {
                limit: parseInt(localStorage.getItem(STORAGE_LIMIT), 10) || 400,
                label: localStorage.getItem(STORAGE_LABEL) || '原稿用紙1枚 (400)',
            };

            var pendingLimit = state.limit;
            var pendingLabel = state.label;
            var saveTimer = null;
            var statusTimer = null;

            function clamp(value, min, max) {
                return Math.max(min, Math.min(max, value));
            }

            function matchingPreset(limit) {
                for (var i = 0; i < PRESETS.length; i++) {
                    if (PRESETS[i].limit === limit) return PRESETS[i];
                }
                return null;
            }

            function labelFor(limit) {
                var preset = matchingPreset(limit);
                return preset ? preset.label + ' (' + preset.limit + ')' : 'カスタム (' + limit + ')';
            }

            function renderPresetGrid() {
                presetGrid.innerHTML = '';
                PRESETS.forEach(function (preset) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'preset-btn';
                    btn.textContent = preset.label + ' (' + preset.limit + ')';
                    btn.dataset.limit = preset.limit;
                    if (preset.limit === pendingLimit) btn.classList.add('active');
                    btn.addEventListener('click', function () {
                        pendingLimit = preset.limit;
                        pendingLabel = preset.label + ' (' + preset.limit + ')';
                        customLimit.value = preset.limit;
                        renderPresetGrid();
                    });
                    presetGrid.appendChild(btn);
                });
            }

            function updateCounter() {
                var length = draft.value.length;
                var over = length > state.limit;
                countText.innerHTML = '<span class="current">' + length + '</span> / ' + state.limit;
                countText.classList.toggle('over', over);
                barFill.style.width = Math.min(100, (length / state.limit) * 100) + '%';
                barFill.classList.toggle('over', over);
                alertBox.classList.toggle('show', over);
                if (over) {
                    document.getElementById('alertText').textContent =
                        '文字数が上限を超えています（' + (length - state.limit) + '字オーバー）';
                }
            }

            function syncHighlightScroll() {
                hlInner.style.transform = 'translateY(' + (-draft.scrollTop) + 'px)';
            }

            function updateExtract() {
                var text = draft.value;
                var len = text.length;
                var start = clamp(parseInt(rangeStart.value, 10) || 0, 0, len);
                var count = clamp(parseInt(rangeLength.value, 10) || 0, 0, len - start);
                var end = start + count;
                var extracted = text.slice(start, end);

                // Highlight overlay in the textarea
                hlInner.textContent = '';
                hlInner.appendChild(document.createTextNode(text.slice(0, start)));
                if (count > 0) {
                    var mark = document.createElement('mark');
                    mark.textContent = extracted;
                    hlInner.appendChild(mark);
                }
                hlInner.appendChild(document.createTextNode(text.slice(end)));
                syncHighlightScroll();

                // Extract card
                if (count > 0) {
                    extractText.textContent = extracted;
                    extractResult.classList.remove('empty');
                    extractMeta.firstChild && (extractMeta.textContent = '');
                    extractMeta.textContent = start + '〜' + (end - 1) + '文字目 ・ ' + count + '文字';
                } else {
                    extractText.textContent = '';
                    extractResult.classList.add('empty');
                    extractMeta.textContent = '該当する文字がありません';
                }

                localStorage.setItem(STORAGE_RANGE_START, start);
                localStorage.setItem(STORAGE_RANGE_LENGTH, count);
            }

            function flashSaved() {
                saveStatus.classList.add('show');
                clearTimeout(statusTimer);
                statusTimer = setTimeout(function () {
                    saveStatus.classList.remove('show');
                }, 1200);
            }

            function persistText() {
                localStorage.setItem(STORAGE_TEXT, draft.value);
                flashSaved();
            }

            function showToast(message) {
                toast.textContent = message;
                toast.classList.add('show');
                setTimeout(function () {
                    toast.classList.remove('show');
                }, 1600);
            }

            function copyToClipboard(text, onDone, onFail) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(onDone, onFail);
                    return;
                }
                try {
                    var temp = document.createElement('textarea');
                    temp.value = text;
                    temp.style.position = 'fixed';
                    temp.style.opacity = '0';
                    document.body.appendChild(temp);
                    temp.select();
                    document.execCommand('copy');
                    document.body.removeChild(temp);
                    onDone();
                } catch (err) {
                    onFail();
                }
            }

            function openModal() {
                pendingLimit = state.limit;
                pendingLabel = state.label;
                customLimit.value = state.limit;
                renderPresetGrid();
                overlay.classList.add('show');
            }

            function closeModal() {
                overlay.classList.remove('show');
            }

            // Init
            draft.value = localStorage.getItem(STORAGE_TEXT) || '';
            presetLabelEl.textContent = state.label;
            var savedRangeStart = localStorage.getItem(STORAGE_RANGE_START);
            var savedRangeLength = localStorage.getItem(STORAGE_RANGE_LENGTH);
            if (savedRangeStart !== null) rangeStart.value = savedRangeStart;
            if (savedRangeLength !== null) rangeLength.value = savedRangeLength;
            updateCounter();
            updateExtract();

            draft.addEventListener('input', function () {
                updateCounter();
                updateExtract();
                clearTimeout(saveTimer);
                saveTimer = setTimeout(persistText, 400);
            });
            draft.addEventListener('scroll', syncHighlightScroll);
            window.addEventListener('resize', syncHighlightScroll);

            rangeStart.addEventListener('input', updateExtract);
            rangeLength.addEventListener('input', updateExtract);

            extractCopyBtn.addEventListener('click', function () {
                var text = extractText.textContent;
                if (!text) {
                    showToast('抽出された文字がありません');
                    return;
                }
                copyToClipboard(text, function () {
                    showToast('抽出範囲をコピーしました');
                }, function () {
                    showToast('コピーに失敗しました');
                });
            });

            settingsBtn.addEventListener('click', openModal);
            cancelBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && overlay.classList.contains('show')) closeModal();
            });

            customLimit.addEventListener('input', function () {
                var value = parseInt(customLimit.value, 10);
                pendingLimit = value > 0 ? value : 0;
                var preset = matchingPreset(pendingLimit);
                pendingLabel = preset ? preset.label + ' (' + preset.limit + ')' : 'カスタム (' + pendingLimit + ')';
                renderPresetGrid();
            });

            saveBtn.addEventListener('click', function () {
                if (!pendingLimit || pendingLimit < 1) {
                    customLimit.focus();
                    return;
                }
                state.limit = pendingLimit;
                state.label = pendingLabel || labelFor(pendingLimit);
                localStorage.setItem(STORAGE_LIMIT, state.limit);
                localStorage.setItem(STORAGE_LABEL, state.label);
                presetLabelEl.textContent = state.label;
                updateCounter();
                closeModal();
            });

            copyBtn.addEventListener('click', function () {
                var text = draft.value;
                if (!text) {
                    showToast('本文が空です');
                    return;
                }
                copyToClipboard(text, function () {
                    showToast('コピーしました');
                }, function () {
                    showToast('コピーに失敗しました');
                });
            });

            clearBtn.addEventListener('click', function () {
                if (!draft.value || confirm('本文をすべて削除しますか？')) {
                    draft.value = '';
                    updateCounter();
                    updateExtract();
                    persistText();
                    draft.focus();
                }
            });
        })();
        </script>
    </body>
</html>
