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

        <title>YT_LOG.exe | YouTube動画タイムスタンプメモ</title>
        @php
            $seoDescription = 'YouTube動画を再生しながら、再生位置のタイムスタンプ付きでメモを記録できる無料ツール。記録内容はブラウザに自動保存。';
            $seoUrl = url('/ytlog');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <meta name="google-site-verification" content="CmL0cYObXLPrUATx_ToU_m4CmOPgTX1rgsKvnIe7aAs" />
        <link rel="canonical" href="{{ $seoUrl }}">
        <meta name="theme-color" content="#2563eb">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="YT_LOG.exe">
        <meta property="og:title" content="YT_LOG.exe | YouTube動画タイムスタンプメモ">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="YT_LOG.exe | YouTube動画タイムスタンプメモ">
        <meta name="twitter:description" content="{{ $seoDescription }}">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "YT_LOG.exe",
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
                background: #fafafa;
                color: #18181b;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Hiragino Sans", "Yu Gothic", sans-serif;
                padding: 1rem;
            }
            .wrap { max-width: 72rem; margin: 0 auto; }
            header {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                gap: 1rem;
                border-bottom: 1px solid #e4e4e7;
                padding-bottom: 1rem;
                margin-bottom: 1.5rem;
            }
            @media (min-width: 768px) { header { flex-direction: row; } }
            header h1 {
                font-size: 1.25rem;
                font-weight: 900;
                letter-spacing: -0.05em;
                font-style: italic;
                color: #2563eb;
                margin: 0;
            }
            #urlInput {
                border: 2px solid #e4e4e7;
                border-radius: 9999px;
                padding: 0.5rem 1.5rem;
                width: 100%;
                outline: none;
                box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            }
            @media (min-width: 768px) { #urlInput { width: 24rem; } }
            #urlInput:focus { border-color: #3b82f6; }
            .grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            @media (min-width: 1024px) { .grid { grid-template-columns: repeat(12, 1fr); } }
            .main-col { display: flex; flex-direction: column; gap: 1rem; }
            @media (min-width: 1024px) { .main-col { grid-column: span 8; } }
            .player-frame {
                aspect-ratio: 16 / 9;
                background: #000;
                border-radius: 1.5rem;
                overflow: hidden;
                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
                border: 8px solid #fff;
            }
            #yt-player { width: 100%; height: 100%; }
            .note-input-row {
                display: flex;
                gap: 0.5rem;
                padding: 0.5rem;
                background: #fff;
                border-radius: 1rem;
                box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08);
                border: 1px solid #f4f4f5;
            }
            #noteInput {
                flex: 1;
                padding: 0.75rem 1rem;
                outline: none;
                font-weight: 500;
                border: none;
                font-family: inherit;
                font-size: 1rem;
            }
            #addNoteBtn {
                background: #2563eb;
                color: #fff;
                padding: 0.75rem 2rem;
                border-radius: 0.75rem;
                font-weight: 700;
                border: none;
                cursor: pointer;
                box-shadow: 0 10px 15px -3px rgba(191,219,254,0.5);
            }
            #addNoteBtn:hover { background: #1d4ed8; }
            .side-col {
                display: flex;
                flex-direction: column;
                height: 600px;
                background: #fff;
                border-radius: 1.5rem;
                border: 1px solid #e4e4e7;
                box-shadow: 0 1px 2px rgba(0,0,0,0.04);
                padding: 1.5rem;
            }
            @media (min-width: 1024px) { .side-col { grid-column: span 4; } }
            #videoInfo {
                display: none;
                margin-bottom: 1.5rem;
                padding: 1rem;
                background: #fafafa;
                border-radius: 1rem;
                border: 1px solid #f4f4f5;
                overflow: hidden;
            }
            #videoInfo .label {
                font-size: 10px;
                font-weight: 700;
                color: #a1a1aa;
                text-transform: uppercase;
                letter-spacing: 0.2em;
                margin: 0 0 0.25rem;
            }
            #videoInfo h2 {
                font-weight: 700;
                font-size: 0.875rem;
                color: #27272a;
                margin: 0 0 0.5rem;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            #videoInfo a {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                font-size: 10px;
                color: #3b82f6;
                font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
                text-decoration: none;
            }
            #videoInfo a:hover { text-decoration: underline; }
            .notes-heading {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-weight: 700;
                color: #a1a1aa;
                margin-bottom: 1rem;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: -0.02em;
            }
            #notesList {
                flex: 1;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                padding-right: 0.5rem;
            }
            .note-item {
                padding: 0.75rem;
                background: #fff;
                border-radius: 0.75rem;
                border: 1px solid #f4f4f5;
                transition: all 0.15s;
            }
            .note-item:hover { border-color: #bfdbfe; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
            .note-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem; }
            .note-time {
                color: #2563eb;
                font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
                font-size: 0.75rem;
                font-weight: 700;
                background: #eff6ff;
                padding: 0.125rem 0.5rem;
                border-radius: 0.25rem;
                border: none;
                cursor: pointer;
            }
            .note-delete {
                color: #d4d4d8;
                background: none;
                border: none;
                cursor: pointer;
                font-size: 0.875rem;
            }
            .note-delete:hover { color: #ef4444; }
            .note-text { font-size: 0.875rem; font-weight: 500; color: #3f3f46; margin: 0; }
        </style>
    </head>
    <body>
        <div class="wrap">
            <header>
                <h1>YT_LOG.exe</h1>
                <input id="urlInput" placeholder="YouTube URLを貼り付けて開始...">
            </header>

            <div class="grid">
                <div class="main-col">
                    <div class="player-frame">
                        <div id="yt-player"></div>
                    </div>
                    <div class="note-input-row">
                        <input id="noteInput" placeholder="変換確定後のEnterで記録...">
                        <button id="addNoteBtn">記録</button>
                    </div>
                </div>

                <div class="side-col">
                    <div id="videoInfo">
                        <p class="label">Recording Video:</p>
                        <h2 id="videoTitle">Loading title...</h2>
                        <a id="videoLink" href="#" target="_blank" rel="noreferrer"></a>
                    </div>

                    <h3 class="notes-heading">Logged Timestamps</h3>
                    <div id="notesList"></div>
                </div>
            </div>
        </div>

        <script>
            let videoId = '';
            let notes = JSON.parse(localStorage.getItem('yt-notes-v2') || '[]');
            let player = null;
            let isApiReady = false;

            const urlInput = document.getElementById('urlInput');
            const noteInput = document.getElementById('noteInput');
            const addNoteBtn = document.getElementById('addNoteBtn');
            const notesList = document.getElementById('notesList');
            const videoInfo = document.getElementById('videoInfo');
            const videoTitleEl = document.getElementById('videoTitle');
            const videoLinkEl = document.getElementById('videoLink');

            function extractVideoId(url) {
                const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:.*v=|\/embed\/))/);
                return match ? (url.split('v=')[1]?.split('&')[0] || url.split('/').pop()) : '';
            }

            function renderNotes() {
                notesList.innerHTML = '';
                notes.forEach((note) => {
                    const item = document.createElement('div');
                    item.className = 'note-item';
                    const mins = Math.floor(note.timestamp / 60);
                    const secs = (note.timestamp % 60).toString().padStart(2, '0');
                    item.innerHTML = `
                        <div class="note-row">
                            <button class="note-time">${mins}:${secs}</button>
                            <button class="note-delete">&#10005;</button>
                        </div>
                        <p class="note-text"></p>
                    `;
                    item.querySelector('.note-text').textContent = note.text;
                    item.querySelector('.note-time').addEventListener('click', () => {
                        if (player) player.seekTo(note.timestamp, true);
                    });
                    item.querySelector('.note-delete').addEventListener('click', () => {
                        notes = notes.filter((n) => n.id !== note.id);
                        localStorage.setItem('yt-notes-v2', JSON.stringify(notes));
                        renderNotes();
                    });
                    notesList.appendChild(item);
                });
            }

            function addNote() {
                if (!noteInput.value || !player) return;
                const currentTime = Math.floor(player.getCurrentTime());
                notes.push({ id: Date.now(), timestamp: currentTime, text: noteInput.value });
                notes.sort((a, b) => a.timestamp - b.timestamp);
                localStorage.setItem('yt-notes-v2', JSON.stringify(notes));
                noteInput.value = '';
                renderNotes();
            }

            addNoteBtn.addEventListener('click', addNote);
            noteInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.isComposing) {
                    e.preventDefault();
                    addNote();
                }
            });

            function createPlayer() {
                if (player) player.destroy();
                videoInfo.style.display = 'block';
                videoTitleEl.textContent = 'Loading title...';
                videoLinkEl.href = `https://www.youtube.com/watch?v=${videoId}`;
                videoLinkEl.textContent = `youtube.com/watch?v=${videoId}`;
                player = new YT.Player('yt-player', {
                    videoId: videoId,
                    events: {
                        onReady: (e) => {
                            videoTitleEl.textContent = e.target.getVideoData().title;
                        },
                    },
                });
            }

            function loadVideo(id) {
                videoId = id;
                localStorage.setItem('yt-last-id', videoId);
                if (isApiReady) createPlayer();
            }

            urlInput.addEventListener('input', (e) => {
                const id = extractVideoId(e.target.value);
                if (id) loadVideo(id);
            });

            window.onYouTubeIframeAPIReady = () => {
                isApiReady = true;
                if (videoId) createPlayer();
            };

            const savedId = localStorage.getItem('yt-last-id');
            if (savedId) videoId = savedId;
            renderNotes();

            const tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            document.body.appendChild(tag);
        </script>
    </body>
</html>
