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

        <title>Debugging Tavern | RPG風エンジニアクイズ</title>
        @php
            $seoDescription = 'セキュリティ・Git・パフォーマンスの3人のNPCと会話しながら、リリース前チェックのクイズに答えるRPG風ミニゲーム。';
            $seoUrl = url('/debugtavern');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <meta name="google-site-verification" content="CmL0cYObXLPrUATx_ToU_m4CmOPgTX1rgsKvnIe7aAs" />
        <link rel="canonical" href="{{ $seoUrl }}">
        <meta name="theme-color" content="#1a1a2e">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="Debugging Tavern">
        <meta property="og:title" content="Debugging Tavern | RPG風エンジニアクイズ">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Debugging Tavern | RPG風エンジニアクイズ">
        <meta name="twitter:description" content="{{ $seoDescription }}">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "Game",
            "name": "Debugging Tavern",
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
                background: #1a1a2e;
                color: #fff;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Hiragino Sans", "Yu Gothic", sans-serif;
                overflow: hidden;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            .stage {
                position: relative;
                width: 100%;
                max-width: 64rem;
                aspect-ratio: 4 / 3;
                background: #252540;
                border-radius: 0.75rem;
                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
                border: 4px solid #3a3a5e;
                overflow: hidden;
            }
            .stage::before {
                content: '';
                position: absolute;
                inset: 0;
                opacity: 0.1;
                background-image: radial-gradient(circle, #ffffff 1px, transparent 1px);
                background-size: 20px 20px;
            }
            .hud-left {
                position: absolute;
                top: 1rem;
                left: 1rem;
                display: flex;
                gap: 1rem;
                z-index: 10;
            }
            .hud-pill {
                background: rgba(0,0,0,0.5);
                padding: 0.5rem 1rem;
                border-radius: 9999px;
                border: 1px solid rgba(255,255,255,0.1);
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-weight: 700;
                color: #fef9c3;
                font-size: 0.875rem;
            }
            .hud-pill .flame { color: #6b7280; }
            .hud-pill .flame.hot { color: #f97316; }
            .hud-right {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: rgba(0,0,0,0.5);
                padding: 1rem;
                border-radius: 0.5rem;
                border: 1px solid rgba(255,255,255,0.1);
                z-index: 10;
                backdrop-filter: blur(4px);
            }
            .hud-right h3 {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #9ca3af;
                margin: 0 0 0.5rem;
            }
            .checklist { display: flex; flex-direction: column; gap: 0.5rem; }
            .checklist-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; }
            .checklist-dot {
                width: 1rem; height: 1rem; border-radius: 9999px;
                border: 1px solid #6b7280; flex-shrink: 0;
            }
            .checklist-dot.done { border: none; background: none; color: #4ade80; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; }
            .checklist-item.done span { color: #6b7280; text-decoration: line-through; }
            .checklist-item span { color: #e5e7eb; }

            .npc {
                position: absolute;
                display: flex;
                flex-direction: column;
                align-items: center;
                cursor: pointer;
                transition: transform 0.15s;
                transform: translate(-50%, -50%);
            }
            .npc:hover { transform: translate(-50%, -50%) scale(1.1); }
            .npc.completed { opacity: 0.5; filter: grayscale(1); }
            .npc-icon {
                width: 4rem; height: 4rem; border-radius: 1rem;
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 4px 0 rgba(0,0,0,0.3);
                border: 2px solid rgba(255,255,255,0.2);
                font-size: 1.75rem;
            }
            .npc-name {
                margin-top: 0.5rem; font-size: 0.75rem; font-weight: 700; color: #9ca3af;
                background: rgba(0,0,0,0.5); padding: 0.125rem 0.5rem; border-radius: 9999px;
            }

            .player {
                position: absolute;
                display: flex;
                flex-direction: column;
                align-items: center;
                z-index: 5;
                pointer-events: none;
                transform: translate(-50%, -50%);
                transition: left 0.6s ease, top 0.6s ease;
            }
            .player-icon {
                width: 3.5rem; height: 3.5rem; border-radius: 9999px;
                background: #6366f1; border: 4px solid #a5b4fc;
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 10px 20px rgba(0,0,0,0.5);
                font-size: 1.25rem;
            }
            .player-name { margin-top: 0.25rem; font-size: 0.75rem; font-weight: 700; color: #a5b4fc; }

            .dialog {
                position: absolute;
                bottom: 2rem; left: 2rem; right: 2rem;
                background: rgba(31,31,53,0.95);
                border: 2px solid #4a4a6e;
                border-radius: 0.75rem;
                padding: 1.5rem;
                box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
                backdrop-filter: blur(6px);
                z-index: 50;
                min-height: 200px;
                display: none;
                flex-direction: column;
            }
            .dialog.open { display: flex; }
            .dialog-head {
                display: flex; align-items: center; gap: 0.75rem;
                margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 1rem;
            }
            .dialog-icon { padding: 0.5rem; border-radius: 0.5rem; font-size: 1.5rem; display: flex; }
            .dialog-head h2 { font-weight: 700; font-size: 1.125rem; margin: 0; color: #fff; }
            .dialog-head p { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin: 0; }
            .dialog-progress { margin-left: auto; display: flex; gap: 0.25rem; }
            .dialog-progress-bar { height: 0.25rem; width: 2rem; border-radius: 9999px; background: #374151; }
            .dialog-progress-bar.active { background: var(--npc-color); }
            .dialog-body { flex: 1; }
            .dialog-question { font-size: 1.25rem; font-weight: 500; margin: 0 0 1.5rem; line-height: 1.6; }
            .options { display: grid; grid-template-columns: 1fr; gap: 1rem; }
            @media (min-width: 768px) { .options { grid-template-columns: 1fr 1fr; } }
            .option-btn {
                padding: 1rem; border-radius: 0.5rem; border: 2px solid rgba(255,255,255,0.1);
                text-align: left; background: rgba(255,255,255,0.05); color: #fff;
                cursor: pointer; font-family: inherit; font-size: 1rem; font-weight: 500;
                transition: all 0.15s;
            }
            .option-btn:hover:not(:disabled) { background: rgba(255,255,255,0.1); border-color: #818cf8; }
            .option-btn:disabled { cursor: default; opacity: 0.6; }

            .feedback {
                position: absolute; inset: 0; z-index: 20;
                background: rgba(0,0,0,0.8); border-radius: 0.5rem;
                display: flex; flex-direction: column; align-items: center; justify-content: center;
                text-align: center; padding: 1.5rem;
            }
            .feedback.correct { color: #4ade80; }
            .feedback.wrong { color: #f87171; }
            .feedback .icon { font-size: 3rem; margin-bottom: 1rem; }
            .feedback .title { font-size: 1.5rem; font-weight: 700; margin: 0 0 0.5rem; }
            .feedback .text { color: #fff; font-size: 1.125rem; max-width: 32rem; margin: 0; }

            .clear-screen {
                position: absolute; inset: 0; background: rgba(0,0,0,0.9); z-index: 60;
                display: none; flex-direction: column; align-items: center; justify-content: center;
                text-align: center; padding: 2rem;
            }
            .clear-screen.open { display: flex; }
            .clear-screen .trophy { font-size: 4rem; margin-bottom: 1.5rem; }
            .clear-screen h1 { font-size: 1.875rem; font-weight: 700; margin: 0 0 1rem; }
            .clear-screen p.sub { color: #9ca3af; font-size: 1.25rem; margin: 0 0 2rem; }
            .clear-screen .final-score { font-size: 1.5rem; font-weight: 700; color: #eab308; margin-bottom: 2rem; }
            .clear-screen button {
                padding: 0.75rem 2rem; background: #4f46e5; color: #fff; border-radius: 9999px;
                font-weight: 700; border: none; cursor: pointer; font-family: inherit;
            }
            .clear-screen button:hover { background: #6366f1; }
        </style>
    </head>
    <body>
        <div class="stage" id="stage">
            <div class="hud-left">
                <div class="hud-pill">&#127942; <span id="scoreDisplay">0</span></div>
                <div class="hud-pill"><span class="flame" id="flameIcon">&#128293;</span> 連続正解: <span id="streakDisplay">0</span></div>
            </div>

            <div class="hud-right">
                <h3>リリース前チェック</h3>
                <div class="checklist" id="checklist"></div>
            </div>

            <div id="npcLayer"></div>

            <div class="player" id="player" style="left:50%; top:80%;">
                <div class="player-icon">&#129496;</div>
                <div class="player-name">あなた</div>
            </div>

            <div class="dialog" id="dialog">
                <div class="dialog-head">
                    <div class="dialog-icon" id="dialogIcon"></div>
                    <div>
                        <h2 id="dialogName"></h2>
                        <p id="dialogRole"></p>
                    </div>
                    <div class="dialog-progress" id="dialogProgress"></div>
                </div>
                <div class="dialog-body">
                    <h3 class="dialog-question" id="dialogQuestion"></h3>
                    <div class="options" id="dialogOptions"></div>
                </div>
                <div class="feedback" id="feedback" style="display:none;">
                    <div class="icon" id="feedbackIcon"></div>
                    <p class="title" id="feedbackTitle"></p>
                    <p class="text" id="feedbackText"></p>
                </div>
            </div>

            <div class="clear-screen" id="clearScreen">
                <div class="trophy">&#127942;</div>
                <h1>リリース前チェック完了！</h1>
                <p class="sub">本番リリース前のバグ潰し、お見事！</p>
                <div class="final-score">最終スコア: <span id="finalScore">0</span></div>
                <button id="retryBtn">もう一度挑戦</button>
            </div>
        </div>

        <script>
            const NPCS = [
                {
                    id: "security", name: "サム", role: "セキュリティの番人", icon: "&#128737;&#65039;",
                    color: "#2563eb", x: 20, y: 30,
                    questions: [
                        {
                            text: "APIキーが含まれた .env ファイルをうっかり公開リポジトリにコミットしてしまった！どうする？",
                            options: [
                                { text: "すぐにリポジトリからファイルを削除する。", isCorrect: false, feedback: "Botは一瞬でGithubをスクレイピングしてるぞ。削除だけじゃ手遅れだ！" },
                                { text: "キーを無効化・再発行して、ファイルを削除する。", isCorrect: true, feedback: "正解！ 漏洩した前提で動くのが鉄則だ。" },
                            ],
                        },
                        {
                            text: "コメント欄にXSSの脆弱性があるとの報告が！",
                            options: [
                                { text: "表示する前に、全ての入力をサニタイズ（無害化）する。", isCorrect: true, feedback: "その通り。ユーザー入力は絶対に信用するな。" },
                                { text: "コメント機能を完全に停止する。", isCorrect: false, feedback: "それはちょっと極端すぎないか？" },
                            ],
                        },
                    ],
                },
                {
                    id: "git", name: "ジーナ", role: "Git オペレーター", icon: "&#127807;",
                    color: "#9333ea", x: 80, y: 40,
                    questions: [
                        {
                            text: "開発ブランチが main から50コミットも遅れてる。履歴をきれいに保ちたいなら？",
                            options: [
                                { text: "git merge main", isCorrect: false, feedback: "マージも動くよ。" },
                                { text: "git rebase main", isCorrect: true, feedback: "正解！ 直線的な履歴の美しさよ。" },
                            ],
                        },
                        {
                            text: "ファイルの変更内容の一部だけをステージングしたい。",
                            options: [
                                { text: "git add -p", isCorrect: true, feedback: "パッチモードは命の恩人ね。" },
                                { text: "git add .", isCorrect: false, feedback: "それだと全部入っちゃうわよ！" },
                            ],
                        },
                    ],
                },
                {
                    id: "performance", name: "ピート", role: "パフォーマンスの鬼", icon: "&#9889;",
                    color: "#f97316", x: 50, y: 15,
                    questions: [
                        {
                            text: "Reactアプリが重い...。リストが無駄に再レンダリングされてるみたいだ。",
                            options: [
                                { text: "リスト項目を React.memo でラップする", isCorrect: true, feedback: "無駄なレンダリングを防ぐ第一歩として最適だね。" },
                                { text: "forceUpdate() を使う", isCorrect: false, feedback: "うわぁ... それは絶対にやめてくれ。" },
                            ],
                        },
                        {
                            text: "APIレスポンスが巨大すぎる。でも必要なのはユーザー名だけだ。",
                            options: [
                                { text: "クライアント側でフィルタリングする。", isCorrect: false, feedback: "帯域の無駄遣いには変わらないぞ！" },
                                { text: "GraphQLか、専用のDTOエンドポイントを使う。", isCorrect: true, feedback: "その通り。" },
                            ],
                        },
                    ],
                },
            ];

            let playerPos = { x: 50, y: 80 };
            let targetNPC = null;
            let isConversing = false;
            let currentQuestionIndex = 0;
            let score = 0;
            let streak = 0;
            let completedNPCs = [];
            let feedback = null;

            const player = document.getElementById('player');
            const npcLayer = document.getElementById('npcLayer');
            const checklist = document.getElementById('checklist');
            const scoreDisplay = document.getElementById('scoreDisplay');
            const streakDisplay = document.getElementById('streakDisplay');
            const flameIcon = document.getElementById('flameIcon');
            const dialog = document.getElementById('dialog');
            const dialogIcon = document.getElementById('dialogIcon');
            const dialogName = document.getElementById('dialogName');
            const dialogRole = document.getElementById('dialogRole');
            const dialogProgress = document.getElementById('dialogProgress');
            const dialogQuestion = document.getElementById('dialogQuestion');
            const dialogOptions = document.getElementById('dialogOptions');
            const feedbackEl = document.getElementById('feedback');
            const feedbackIcon = document.getElementById('feedbackIcon');
            const feedbackTitle = document.getElementById('feedbackTitle');
            const feedbackText = document.getElementById('feedbackText');
            const clearScreen = document.getElementById('clearScreen');
            const finalScore = document.getElementById('finalScore');

            function renderNpcs() {
                npcLayer.innerHTML = '';
                NPCS.forEach((npc) => {
                    const done = completedNPCs.includes(npc.id);
                    const el = document.createElement('div');
                    el.className = 'npc' + (done ? ' completed' : '');
                    el.style.left = npc.x + '%';
                    el.style.top = npc.y + '%';
                    el.innerHTML = `
                        <div class="npc-icon" style="background:${npc.color}">${npc.icon}</div>
                        <div class="npc-name">${npc.name}</div>
                    `;
                    el.addEventListener('click', () => moveToNpc(npc));
                    npcLayer.appendChild(el);
                });
            }

            function renderChecklist() {
                checklist.innerHTML = '';
                NPCS.forEach((npc) => {
                    const done = completedNPCs.includes(npc.id);
                    const row = document.createElement('div');
                    row.className = 'checklist-item' + (done ? ' done' : '');
                    row.innerHTML = `
                        <div class="checklist-dot${done ? ' done' : ''}">${done ? '&#10003;' : ''}</div>
                        <span>${npc.role}</span>
                    `;
                    checklist.appendChild(row);
                });
            }

            function updateHud() {
                scoreDisplay.textContent = score;
                streakDisplay.textContent = streak;
                flameIcon.classList.toggle('hot', streak > 2);
            }

            function moveToNpc(npc) {
                if (isConversing) return;
                playerPos = { x: npc.x, y: npc.y + 10 };
                player.style.left = playerPos.x + '%';
                player.style.top = playerPos.y + '%';
                targetNPC = npc;
                setTimeout(() => {
                    isConversing = true;
                    currentQuestionIndex = 0;
                    feedback = null;
                    openDialog();
                }, 600);
            }

            function openDialog() {
                dialog.classList.add('open');
                dialogIcon.style.background = targetNPC.color;
                dialogIcon.innerHTML = targetNPC.icon;
                dialogName.textContent = targetNPC.name;
                dialogRole.textContent = targetNPC.role;
                renderQuestion();
            }

            function renderQuestion() {
                feedbackEl.style.display = 'none';
                dialogProgress.innerHTML = '';
                targetNPC.questions.forEach((_, i) => {
                    const bar = document.createElement('div');
                    bar.className = 'dialog-progress-bar' + (i <= currentQuestionIndex ? ' active' : '');
                    if (i <= currentQuestionIndex) bar.style.background = targetNPC.color;
                    dialogProgress.appendChild(bar);
                });

                const q = targetNPC.questions[currentQuestionIndex];
                dialogQuestion.textContent = q.text;
                dialogOptions.innerHTML = '';
                q.options.forEach((option) => {
                    const btn = document.createElement('button');
                    btn.className = 'option-btn';
                    btn.textContent = option.text;
                    btn.addEventListener('click', () => handleAnswer(option.isCorrect, option.feedback));
                    dialogOptions.appendChild(btn);
                });
            }

            function handleAnswer(isCorrect, feedbackTextVal) {
                document.querySelectorAll('.option-btn').forEach((b) => b.disabled = true);
                feedback = { text: feedbackTextVal, isCorrect };

                if (isCorrect) {
                    score += 100 + streak * 10;
                    streak += 1;
                } else {
                    streak = 0;
                }
                updateHud();

                feedbackEl.style.display = 'flex';
                feedbackEl.className = 'feedback ' + (isCorrect ? 'correct' : 'wrong');
                feedbackIcon.innerHTML = isCorrect ? '&#9989;' : '&#10060;';
                feedbackTitle.textContent = isCorrect ? '正解！' : '残念...';
                feedbackText.textContent = feedbackTextVal;

                setTimeout(() => {
                    if (isCorrect) {
                        nextQuestion();
                    } else {
                        feedback = null;
                        feedbackEl.style.display = 'none';
                        document.querySelectorAll('.option-btn').forEach((b) => b.disabled = false);
                    }
                }, 2000);
            }

            function nextQuestion() {
                feedback = null;
                if (targetNPC && currentQuestionIndex < targetNPC.questions.length - 1) {
                    currentQuestionIndex += 1;
                    renderQuestion();
                } else {
                    if (targetNPC && !completedNPCs.includes(targetNPC.id)) {
                        completedNPCs.push(targetNPC.id);
                    }
                    isConversing = false;
                    targetNPC = null;
                    dialog.classList.remove('open');
                    renderNpcs();
                    renderChecklist();
                    checkAllComplete();
                }
            }

            function checkAllComplete() {
                if (completedNPCs.length === NPCS.length) {
                    finalScore.textContent = score;
                    clearScreen.classList.add('open');
                }
            }

            document.getElementById('retryBtn').addEventListener('click', () => window.location.reload());

            renderNpcs();
            renderChecklist();
            updateHud();
        </script>
    </body>
</html>
