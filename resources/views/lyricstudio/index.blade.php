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

        <title>Lyric Studio | 歌詞メモ + 曲情報アーカイブ</title>
        @php
            $seoDescription = '好きな曲の歌詞を貼り付けて保存できる無料ツール。iTunes Search APIで曲名・アーティスト・ジャケット画像を検索して紐付け、YouTube Musicへのリンクも自動生成します。';
            $seoUrl = url('/lyricstudio');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <meta name="google-site-verification" content="CmL0cYObXLPrUATx_ToU_m4CmOPgTX1rgsKvnIe7aAs" />
        <link rel="canonical" href="{{ $seoUrl }}">
        <meta name="theme-color" content="#dc2626">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="Lyric Studio">
        <meta property="og:title" content="Lyric Studio | 歌詞メモ + 曲情報アーカイブ">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Lyric Studio | 歌詞メモ + 曲情報アーカイブ">
        <meta name="twitter:description" content="{{ $seoDescription }}">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "Lyric Studio",
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
                background: #030003;
                color: #f4f4f5;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Hiragino Sans", "Yu Gothic", sans-serif;
                padding: 1rem;
            }
            @media (min-width: 768px) { body { padding: 2.5rem; } }
            ::selection { background: rgba(220,38,38,0.3); }
            .wrap { max-width: 72rem; margin: 0 auto; }
            header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 3rem;
            }
            h1 {
                font-size: 1.875rem;
                font-weight: 900;
                color: #dc2626;
                font-style: italic;
                letter-spacing: -0.05em;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .grid-2 {
                display: grid;
                grid-template-columns: 1fr;
                gap: 2rem;
                margin-bottom: 2.5rem;
            }
            @media (min-width: 1024px) { .grid-2 { grid-template-columns: 1fr 1fr; } }
            .step-label {
                font-size: 10px;
                font-weight: 700;
                color: #71717a;
                letter-spacing: 0.2em;
                text-transform: uppercase;
                display: block;
                margin-bottom: 0.75rem;
            }
            textarea {
                width: 100%;
                height: 20rem;
                background: #18181b;
                border: 1px solid #27272a;
                border-radius: 1.5rem;
                padding: 1.5rem;
                outline: none;
                font-size: 1.125rem;
                line-height: 1.625;
                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
                color: #f4f4f5;
                font-family: inherit;
                resize: none;
                transition: box-shadow 0.15s;
            }
            textarea:focus { box-shadow: 0 0 0 2px #dc2626; }
            .search-wrap { position: relative; }
            #searchInput {
                width: 100%;
                background: #18181b;
                border: 1px solid #27272a;
                padding: 1.25rem;
                border-radius: 1rem;
                outline: none;
                font-size: 1.25rem;
                color: #f4f4f5;
                font-family: inherit;
            }
            #searchInput:focus { border-color: #dc2626; }
            #results {
                display: none;
                position: absolute;
                top: 100%;
                margin-top: 0.5rem;
                width: 100%;
                background: #18181b;
                border: 1px solid #27272a;
                border-radius: 1rem;
                overflow: hidden;
                z-index: 50;
                box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            }
            .result-item {
                width: 100%;
                padding: 1rem;
                display: flex;
                align-items: center;
                gap: 1rem;
                background: none;
                border: none;
                border-bottom: 1px solid rgba(39,39,42,0.5);
                cursor: pointer;
                text-align: left;
                color: inherit;
                font-family: inherit;
            }
            .result-item:hover { background: #27272a; }
            .result-item img { width: 48px; height: 48px; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
            .result-item .rt { min-width: 0; flex: 1; }
            .result-item .rt-title { font-size: 0.875rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .result-item .rt-artist { color: #71717a; font-weight: 400; display: block; font-size: 0.8rem; }

            #selectedBox {
                display: none;
                margin-top: 1.5rem;
                padding: 1.25rem;
                background: rgba(220,38,38,0.1);
                border: 1px solid rgba(220,38,38,0.2);
                border-radius: 1.5rem;
                align-items: center;
                gap: 1rem;
            }
            #selectedBox img { width: 64px; height: 64px; border-radius: 0.75rem; box-shadow: 0 10px 15px rgba(0,0,0,0.3); }
            #selectedBox .st { flex: 1; min-width: 0; }
            #selectedBox .st-title { font-weight: 900; font-size: 1.125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            #selectedBox .st-artist { color: #ef4444; font-weight: 700; }
            #selectedBox svg { color: #dc2626; flex-shrink: 0; }

            #saveBtn {
                width: 100%;
                margin-top: 1.5rem;
                background: #f4f4f5;
                color: #000;
                padding: 1.25rem;
                border-radius: 9999px;
                font-weight: 900;
                font-size: 1.125rem;
                border: none;
                cursor: pointer;
                transition: all 0.15s;
                box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3);
            }
            #saveBtn:hover { background: #fff; transform: scale(1.01); }

            #songGrid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            @media (min-width: 768px) { #songGrid { grid-template-columns: 1fr 1fr; } }
            @media (min-width: 1280px) { #songGrid { grid-template-columns: 1fr 1fr 1fr; } }
            .song-card {
                background: #18181b;
                border-radius: 2.5rem;
                padding: 1.5rem;
                border: 1px solid #27272a;
                position: relative;
                transition: border-color 0.15s;
            }
            .song-card:hover { border-color: rgba(220,38,38,0.5); }
            .song-head { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
            .song-head img { width: 96px; height: 96px; border-radius: 1rem; object-fit: cover; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
            .song-meta { flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center; }
            .song-meta h3 { font-weight: 700; font-size: 1.25rem; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0; }
            .song-meta p.artist { color: #dc2626; font-weight: 700; font-size: 0.875rem; margin: 0.25rem 0 0.5rem; }
            .listen-link {
                display: inline-flex;
                align-items: center;
                gap: 0.375rem;
                font-size: 10px;
                font-weight: 900;
                background: #27272a;
                color: #d4d4d8;
                width: fit-content;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                text-decoration: none;
                text-transform: uppercase;
            }
            .listen-link:hover { background: #dc2626; color: #fff; }
            .song-lyrics {
                height: 12rem;
                overflow-y: auto;
                font-size: 0.875rem;
                color: #a1a1aa;
                font-style: italic;
                line-height: 1.625;
                white-space: pre-wrap;
                border-top: 1px solid #27272a;
                padding-top: 1rem;
            }
            .delete-btn {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: none;
                border: none;
                color: #3f3f46;
                cursor: pointer;
            }
            .delete-btn:hover { color: #ef4444; }
        </style>
    </head>
    <body>
        <div class="wrap">
            <header>
                <h1>
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor"><path d="M21.58 7.19a2.51 2.51 0 0 0-1.77-1.78C18.25 5 12 5 12 5s-6.25 0-7.81.41a2.51 2.51 0 0 0-1.77 1.78A26.16 26.16 0 0 0 2 12a26.16 26.16 0 0 0 .42 4.81 2.51 2.51 0 0 0 1.77 1.78C5.75 19 12 19 12 19s6.25 0 7.81-.41a2.51 2.51 0 0 0 1.77-1.78A26.16 26.16 0 0 0 22 12a26.16 26.16 0 0 0-.42-4.81zM10 15V9l5.2 3z"/></svg>
                    LYRIC STUDIO
                </h1>
            </header>

            <div class="grid-2">
                <div>
                    <span class="step-label">Step 1. Paste Lyrics</span>
                    <textarea id="lyricsInput" placeholder="ここに歌詞を貼り付け..."></textarea>
                </div>

                <div>
                    <span class="step-label">Step 2. Connect Music</span>
                    <div class="search-wrap">
                        <input id="searchInput" placeholder="曲名・アーティスト名で検索...">
                        <div id="results"></div>
                    </div>

                    <div id="selectedBox">
                        <img id="selectedImg" src="" alt="">
                        <div class="st">
                            <p class="st-title" id="selectedTitle"></p>
                            <p class="st-artist" id="selectedArtist"></p>
                        </div>
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>

                    <button id="saveBtn">SAVE ARCHIVE</button>
                </div>
            </div>

            <div id="songGrid"></div>
        </div>

        <script>
            const STORAGE_KEY = 'my-lyrics-v5';
            let songs = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
            let selected = null;
            let searchTimer = null;

            const lyricsInput = document.getElementById('lyricsInput');
            const searchInput = document.getElementById('searchInput');
            const results = document.getElementById('results');
            const selectedBox = document.getElementById('selectedBox');
            const selectedImg = document.getElementById('selectedImg');
            const selectedTitle = document.getElementById('selectedTitle');
            const selectedArtist = document.getElementById('selectedArtist');
            const saveBtn = document.getElementById('saveBtn');
            const songGrid = document.getElementById('songGrid');

            function renderSongs() {
                songGrid.innerHTML = '';
                songs.forEach((song) => {
                    const card = document.createElement('div');
                    card.className = 'song-card';
                    card.innerHTML = `
                        <div class="song-head">
                            <img src="${song.cover}" alt="">
                            <div class="song-meta">
                                <h3></h3>
                                <p class="artist"></p>
                                <a class="listen-link" href="${song.ytmUrl}" target="_blank" rel="noreferrer">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M21.58 7.19a2.51 2.51 0 0 0-1.77-1.78C18.25 5 12 5 12 5s-6.25 0-7.81.41a2.51 2.51 0 0 0-1.77 1.78A26.16 26.16 0 0 0 2 12a26.16 26.16 0 0 0 .42 4.81 2.51 2.51 0 0 0 1.77 1.78C5.75 19 12 19 12 19s6.25 0 7.81-.41a2.51 2.51 0 0 0 1.77-1.78A26.16 26.16 0 0 0 22 12a26.16 26.16 0 0 0-.42-4.81zM10 15V9l5.2 3z"/></svg>
                                    Listen
                                </a>
                            </div>
                        </div>
                        <div class="song-lyrics"></div>
                        <button class="delete-btn">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                        </button>
                    `;
                    card.querySelector('h3').textContent = song.title;
                    card.querySelector('.artist').textContent = song.artist;
                    card.querySelector('.song-lyrics').textContent = song.lyrics;
                    card.querySelector('.delete-btn').addEventListener('click', () => {
                        songs = songs.filter((s) => s.id !== song.id);
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(songs));
                        renderSongs();
                    });
                    songGrid.appendChild(card);
                });
            }

            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);
                const query = searchInput.value;
                if (query.length <= 1) {
                    results.style.display = 'none';
                    results.innerHTML = '';
                    return;
                }
                searchTimer = setTimeout(async () => {
                    try {
                        const res = await fetch(`https://itunes.apple.com/search?term=${encodeURIComponent(query)}&entity=song&limit=5`);
                        const data = await res.json();
                        renderResults(data.results || []);
                    } catch (e) {
                        results.style.display = 'none';
                    }
                }, 500);
            });

            function renderResults(items) {
                if (items.length === 0) {
                    results.style.display = 'none';
                    results.innerHTML = '';
                    return;
                }
                results.innerHTML = '';
                items.forEach((r) => {
                    const btn = document.createElement('button');
                    btn.className = 'result-item';
                    btn.innerHTML = `
                        <img src="${r.artworkUrl60}" alt="">
                        <div class="rt">
                            <div class="rt-title"></div>
                        </div>
                    `;
                    const titleDiv = btn.querySelector('.rt-title');
                    titleDiv.textContent = r.trackName;
                    const artistSpan = document.createElement('span');
                    artistSpan.className = 'rt-artist';
                    artistSpan.textContent = r.artistName;
                    titleDiv.appendChild(artistSpan);

                    btn.addEventListener('click', () => {
                        selected = r;
                        results.style.display = 'none';
                        results.innerHTML = '';
                        searchInput.value = '';
                        selectedImg.src = r.artworkUrl60;
                        selectedTitle.textContent = r.trackName;
                        selectedArtist.textContent = r.artistName;
                        selectedBox.style.display = 'flex';
                    });
                    results.appendChild(btn);
                });
                results.style.display = 'block';
            }

            saveBtn.addEventListener('click', () => {
                const lyrics = lyricsInput.value;
                if (!selected || !lyrics) {
                    alert('曲を選択し、歌詞を入力してください');
                    return;
                }

                const encodedSearch = encodeURIComponent(`${selected.trackName} ${selected.artistName}`);
                const ytmUrl = `https://music.youtube.com/search?q=${encodedSearch}`;

                const newEntry = {
                    id: Date.now(),
                    title: selected.trackName,
                    artist: selected.artistName,
                    cover: selected.artworkUrl100.replace('100x100bb', '400x400bb'),
                    lyrics,
                    ytmUrl,
                };

                songs = [newEntry, ...songs];
                localStorage.setItem(STORAGE_KEY, JSON.stringify(songs));
                renderSongs();

                lyricsInput.value = '';
                searchInput.value = '';
                selected = null;
                selectedBox.style.display = 'none';
            });

            renderSongs();
        </script>
    </body>
</html>
