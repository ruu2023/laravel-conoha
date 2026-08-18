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

        <title>NEO DASH | ワンクリック重力回避ゲーム</title>
        @php
            $seoDescription = 'クリック(タップ)だけで遊べる、重力とジャンプで障害物を避け続けるブラウザアクションゲーム。ハイスコアはブラウザに自動保存。';
            $seoUrl = url('/gravitydash');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <meta name="google-site-verification" content="CmL0cYObXLPrUATx_ToU_m4CmOPgTX1rgsKvnIe7aAs" />
        <link rel="canonical" href="{{ $seoUrl }}">
        <meta name="theme-color" content="#0f172a">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="NEO DASH">
        <meta property="og:title" content="NEO DASH | ワンクリック重力回避ゲーム">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="NEO DASH | ワンクリック重力回避ゲーム">
        <meta name="twitter:description" content="{{ $seoDescription }}">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "Game",
            "name": "NEO DASH",
            "url": "{{ $seoUrl }}",
            "description": "{{ $seoDescription }}",
            "inLanguage": "ja"
        }
        </script>

        <style>
            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: #020617;
                color: #fff;
                font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
                user-select: none;
                padding: 1rem;
                transition: background-color 0.7s;
            }
            body.state-gameover { background: #450a0a; }
            body.score-30 { background: #831843; }
            body.score-20 { background: #581c87; }
            body.score-10 { background: #312e81; }

            .title-wrap { text-align: center; margin-bottom: 1.5rem; }
            h1 {
                font-size: 2.25rem;
                font-weight: 900;
                letter-spacing: -0.05em;
                font-style: italic;
                margin: 0;
            }
            h1 .accent { color: #60a5fa; }
            .record {
                font-size: 0.75rem;
                opacity: 0.5;
                text-transform: uppercase;
                letter-spacing: 0.2em;
                margin-top: 0.25rem;
            }
            .stage {
                position: relative;
                border: 2px solid rgba(255,255,255,0.2);
                border-radius: 1rem;
                overflow: hidden;
                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
                background: rgba(0,0,0,0.2);
                cursor: pointer;
            }
            canvas { display: block; max-width: 100%; }
            .score-hud {
                position: absolute;
                top: 1rem;
                left: 1.5rem;
            }
            .score-hud .num { font-size: 1.875rem; font-weight: 900; margin: 0; }
            .score-hud .label { font-size: 10px; color: #93c5fd; margin: 0; }
            .overlay {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: rgba(0,0,0,0.6);
                backdrop-filter: blur(4px);
            }
            .overlay h2 {
                font-size: 3.5rem;
                font-weight: 900;
                font-style: italic;
                margin: 0 0 1rem;
            }
            .overlay button {
                padding: 0.75rem 2.5rem;
                background: #fff;
                color: #000;
                font-weight: 700;
                border: none;
                cursor: pointer;
                transition: all 0.2s;
                font-family: inherit;
            }
            .overlay button:hover { background: #60a5fa; color: #fff; }
            .meta-row {
                margin-top: 2rem;
                display: flex;
                gap: 1.5rem;
                font-size: 10px;
                opacity: 0.4;
                text-transform: uppercase;
            }
            .meta-row span { color: #fff; }
        </style>
    </head>
    <body>
        <div class="title-wrap">
            <h1>NEO <span class="accent">DASH</span></h1>
            <div class="record">Record: <span id="highScore">0</span> pts</div>
        </div>

        <div class="stage" id="stage">
            <canvas id="canvas" width="600" height="300"></canvas>

            <div class="score-hud">
                <p class="num" id="scoreDisplay">0</p>
                <p class="label">SCORE</p>
            </div>

            <div class="overlay" id="overlay">
                <h2 id="overlayTitle">READY?</h2>
                <button id="overlayBtn">SYNC ENGINE</button>
            </div>
        </div>

        <div class="meta-row">
            <p>Avoid streaks: <span>Enabled</span></p>
            <p>Speed: <span id="speedDisplay">x1.0</span></p>
        </div>

        <script>
            const GRAVITY = 0.5;
            const JUMP_FORCE = -8;
            const BASE_SPEED = 5;
            const MAX_SPEED = 12;
            const SPAWN_INTERVAL = 1400;

            const canvas = document.getElementById('canvas');
            const ctx = canvas.getContext('2d');
            const stage = document.getElementById('stage');
            const overlay = document.getElementById('overlay');
            const overlayTitle = document.getElementById('overlayTitle');
            const overlayBtn = document.getElementById('overlayBtn');
            const scoreDisplay = document.getElementById('scoreDisplay');
            const highScoreDisplay = document.getElementById('highScore');
            const speedDisplay = document.getElementById('speedDisplay');

            let gameState = 'START';
            let score = 0;
            let highScore = 0;
            let requestId = null;
            let lastSpawn = 0;

            let engine = { playerY: 150, playerVy: 0, score: 0, obstacles: [], lastPosIsTop: false, consecutiveCount: 0 };

            function updateBg() {
                document.body.classList.remove('state-gameover', 'score-30', 'score-20', 'score-10');
                if (gameState === 'GAMEOVER') document.body.classList.add('state-gameover');
                else if (score >= 30) document.body.classList.add('score-30');
                else if (score >= 20) document.body.classList.add('score-20');
                else if (score >= 10) document.body.classList.add('score-10');
            }

            function setScore(s) {
                score = s;
                scoreDisplay.textContent = score;
                speedDisplay.textContent = 'x' + (1 + Math.floor(score / 5) * 0.1).toFixed(1);
                if (score > highScore) {
                    highScore = score;
                    highScoreDisplay.textContent = highScore;
                }
                updateBg();
            }

            function startGame() {
                if (requestId) cancelAnimationFrame(requestId);
                engine = { playerY: 150, playerVy: 0, score: 0, obstacles: [], lastPosIsTop: false, consecutiveCount: 0 };
                lastSpawn = performance.now();
                setScore(0);
                gameState = 'PLAYING';
                overlay.style.display = 'none';
                requestId = requestAnimationFrame(update);
            }

            function endGame() {
                gameState = 'GAMEOVER';
                updateBg();
                overlayTitle.textContent = 'FAIL';
                overlayBtn.textContent = 'RETRY SYSTEM';
                overlay.style.display = 'flex';
            }

            function update(time) {
                if (gameState !== 'PLAYING') return;
                const e = engine;
                const currentSpeed = Math.min(BASE_SPEED + Math.floor(e.score / 5) * 0.7, MAX_SPEED);

                e.playerVy += GRAVITY;
                e.playerY += e.playerVy;
                if (e.playerY > canvas.height - 30) { e.playerY = canvas.height - 30; e.playerVy = 0; }
                if (e.playerY < 0) { e.playerY = 0; e.playerVy = 0; }

                if (time - lastSpawn > SPAWN_INTERVAL - (currentSpeed * 50)) {
                    let isTop = Math.random() > 0.5;
                    if (isTop === e.lastPosIsTop) {
                        e.consecutiveCount++;
                        if (e.consecutiveCount >= 2) { isTop = !isTop; e.consecutiveCount = 0; }
                    } else {
                        e.consecutiveCount = 0;
                    }
                    e.lastPosIsTop = isTop;
                    e.obstacles.push({ x: canvas.width, width: 35, height: 60 + Math.random() * 90, isTop });
                    lastSpawn = time;
                }

                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (let i = e.obstacles.length - 1; i >= 0; i--) {
                    const obs = e.obstacles[i];
                    obs.x -= currentSpeed;
                    const obsY = obs.isTop ? 0 : canvas.height - obs.height;

                    if (55 < obs.x + obs.width && 75 > obs.x && e.playerY + 5 < obsY + obs.height && e.playerY + 25 > obsY) {
                        endGame();
                        return;
                    }

                    if (obs.x + obs.width < 0) {
                        e.obstacles.splice(i, 1);
                        e.score++;
                        setScore(e.score);
                    }

                    ctx.fillStyle = obs.isTop ? '#fca5a5' : '#f87171';
                    ctx.fillRect(obs.x, obsY, obs.width, obs.height);
                }

                ctx.fillStyle = '#60a5fa';
                ctx.shadowBlur = 10;
                ctx.shadowColor = '#60a5fa';
                ctx.fillRect(50, e.playerY, 30, 30);
                ctx.shadowBlur = 0;

                requestId = requestAnimationFrame(update);
            }

            stage.addEventListener('click', () => {
                if (gameState === 'PLAYING') {
                    engine.playerVy = JUMP_FORCE;
                } else {
                    startGame();
                }
            });
        </script>
    </body>
</html>
