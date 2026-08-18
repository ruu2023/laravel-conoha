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

        <title>MyBatis 30分マスター講義 | Java開発者向けSQLマッピング入門</title>
        @php
            $seoDescription = 'MyBatisの設定・XMLマッパー・アノテーション・SqlSessionの実行までを4ステップで学べる、進捗自動保存つきの無料学習アプリ。';
            $seoUrl = url('/mybatistutor');
        @endphp
        <meta name="description" content="{{ $seoDescription }}">
        <meta name="robots" content="index, follow">
        <meta name="google-site-verification" content="CmL0cYObXLPrUATx_ToU_m4CmOPgTX1rgsKvnIe7aAs" />
        <link rel="canonical" href="{{ $seoUrl }}">
        <meta name="theme-color" content="#4f46e5">

        <meta property="og:type" content="website">
        <meta property="og:locale" content="ja_JP">
        <meta property="og:site_name" content="MyBatis 30分マスター講義">
        <meta property="og:title" content="MyBatis 30分マスター講義 | Java開発者向けSQLマッピング入門">
        <meta property="og:description" content="{{ $seoDescription }}">
        <meta property="og:url" content="{{ $seoUrl }}">

        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="MyBatis 30分マスター講義 | Java開発者向けSQLマッピング入門">
        <meta name="twitter:description" content="{{ $seoDescription }}">

        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "LearningResource",
            "name": "MyBatis 30分マスター講義",
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
                background: #f9fafb;
                color: #1f2937;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Hiragino Sans", "Yu Gothic", sans-serif;
                padding: 1rem;
            }
            @media (min-width: 768px) { body { padding: 2rem; } }
            .card {
                max-width: 48rem;
                margin: 0 auto;
                background: #fff;
                border-radius: 0.75rem;
                box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
                overflow: hidden;
            }
            .head {
                background: #4f46e5;
                color: #fff;
                padding: 1.5rem;
            }
            .head h1 { font-size: 1.5rem; font-weight: 700; margin: 0; }
            .head p { margin: 0.5rem 0 0; color: #c7d2fe; }
            .progress-bar {
                margin-top: 1rem;
                background: #3730a3;
                border-radius: 9999px;
                height: 0.5rem;
                width: 100%;
                overflow: hidden;
            }
            .progress-fill {
                background: #4ade80;
                height: 100%;
                border-radius: 9999px;
                transition: width 0.5s;
                width: 0%;
            }
            .body-flex {
                display: flex;
                flex-direction: column;
                min-height: 400px;
            }
            @media (min-width: 768px) { .body-flex { flex-direction: row; } }
            nav {
                width: 100%;
                background: #f3f4f6;
                border-right: 1px solid #e5e7eb;
                padding: 1rem;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            @media (min-width: 768px) { nav { width: 33.333%; } }
            .step-btn {
                width: 100%;
                text-align: left;
                padding: 0.75rem;
                border-radius: 0.25rem;
                border: none;
                background: transparent;
                cursor: pointer;
                transition: background 0.15s;
                font-family: inherit;
            }
            .step-btn:hover { background: #e5e7eb; }
            .step-btn.active {
                background: #fff;
                box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
                border-left: 4px solid #6366f1;
            }
            .step-btn .step-num {
                font-size: 0.875rem;
                color: #6b7280;
                display: block;
            }
            .step-btn .step-num.done { color: #16a34a; font-weight: 700; }
            .step-btn .step-title {
                font-size: 0.875rem;
                font-weight: 500;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .lesson {
                width: 100%;
                padding: 1.5rem;
                display: flex;
                flex-direction: column;
            }
            @media (min-width: 768px) { .lesson { width: 66.667%; } }
            .lesson h2 { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 0 0 0.5rem; }
            .lesson p.desc { color: #4b5563; margin: 0 0 1.5rem; }
            .code-wrap { position: relative; margin-bottom: 1.5rem; }
            .code-tag {
                position: absolute;
                top: -0.75rem;
                right: 1rem;
                background: #1f2937;
                color: #fff;
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
            }
            pre {
                background: #111827;
                color: #f3f4f6;
                padding: 1rem;
                border-radius: 0.5rem;
                overflow-x: auto;
                font-size: 0.875rem;
                line-height: 1.625;
                border: 1px solid #374151;
                margin: 0;
                font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            }
            .footer-row {
                margin-top: auto;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .footer-row span { font-size: 0.875rem; color: #6b7280; }
            .done-btn {
                background: #4f46e5;
                color: #fff;
                font-weight: 700;
                padding: 0.5rem 1.5rem;
                border-radius: 0.5rem;
                border: none;
                cursor: pointer;
                transition: background 0.15s;
            }
            .done-btn:hover { background: #4338ca; }
            footer.tip {
                max-width: 48rem;
                margin: 2rem auto 0;
                text-align: center;
                color: #9ca3af;
                font-size: 0.875rem;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="head">
                <h1>MyBatis 30分マスター講義</h1>
                <p>Java開発者のためのSQLマッピング集中講座</p>
                <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
            </div>

            <div class="body-flex">
                <nav id="nav"></nav>
                <div class="lesson">
                    <h2 id="lessonTitle"></h2>
                    <p class="desc" id="lessonDesc"></p>
                    <div class="code-wrap">
                        <span class="code-tag">Code</span>
                        <pre><code id="lessonCode"></code></pre>
                    </div>
                    <div class="footer-row">
                        <span id="progressText"></span>
                        <button class="done-btn" id="doneBtn"></button>
                    </div>
                </div>
            </div>
        </div>

        <footer class="tip">
            <p>Tip: MyBatisでは #{variable} を使うことでSQLインジェクションを防ぐプリペアドステートメントが生成されます。</p>
        </footer>

        <script>
            const LESSONS = [
                {
                    title: "1. 設定 (mybatis-config.xml)",
                    description: "データベース接続やマッパーの登録を行います。",
                    code: `<configuration>
  <environments default="development">
    <environment id="development">
      <transactionManager type="JDBC"/>
      <dataSource type="POOLED">
        <property name="driver" value="com.mysql.cj.jdbc.Driver"/>
      </dataSource>
    </environment>
  </environments>
</configuration>`,
                },
                {
                    title: "2. XMLマッパー (UserMapper.xml)",
                    description: "SQLをXMLに記述し、IDでJavaから呼び出せるようにします。",
                    code: `<mapper namespace="com.example.UserMapper">
  <select id="getUser" resultType="User">
    SELECT * FROM users WHERE id = #{id}
  </select>
</mapper>`,
                },
                {
                    title: "3. アノテーション (Interface)",
                    description: "簡単なSQLなら、Javaインターフェースに直接記述することも可能です。",
                    code: `public interface UserMapper {
  @Select("SELECT * FROM users WHERE id = #{id}")
  User getUser(int id);
}`,
                },
                {
                    title: "4. 実行 (SqlSession)",
                    description: "セッションを開いて、マッパーを実行します。",
                    code: `try (SqlSession session = sqlSessionFactory.openSession()) {
  UserMapper mapper = session.getMapper(UserMapper.class);
  User user = mapper.getUser(1);
}`,
                },
            ];

            const STORAGE_KEY = 'mybatis-progress';
            let currentStep = 0;
            let completed = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

            const nav = document.getElementById('nav');
            const lessonTitle = document.getElementById('lessonTitle');
            const lessonDesc = document.getElementById('lessonDesc');
            const lessonCode = document.getElementById('lessonCode');
            const progressText = document.getElementById('progressText');
            const progressFill = document.getElementById('progressFill');
            const doneBtn = document.getElementById('doneBtn');

            function renderNav() {
                nav.innerHTML = '';
                LESSONS.forEach((lesson, index) => {
                    const btn = document.createElement('button');
                    btn.className = 'step-btn' + (currentStep === index ? ' active' : '');
                    const done = completed.includes(index);
                    btn.innerHTML = `
                        <span class="step-num${done ? ' done' : ''}">${done ? '✓ ' : ''}Step ${index + 1}</span>
                        <div class="step-title">${lesson.title}</div>
                    `;
                    btn.addEventListener('click', () => {
                        currentStep = index;
                        render();
                    });
                    nav.appendChild(btn);
                });
            }

            function render() {
                renderNav();
                const lesson = LESSONS[currentStep];
                lessonTitle.textContent = lesson.title;
                lessonDesc.textContent = lesson.description;
                lessonCode.textContent = lesson.code;
                progressText.textContent = `進捗: ${completed.length} / ${LESSONS.length} 完了`;
                progressFill.style.width = (completed.length / LESSONS.length * 100) + '%';
                doneBtn.textContent = completed.includes(currentStep) ? '次へ進む' : '理解した!';
            }

            doneBtn.addEventListener('click', () => {
                if (!completed.includes(currentStep)) {
                    completed = [...completed, currentStep];
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(completed));
                }
                if (currentStep < LESSONS.length - 1) {
                    currentStep += 1;
                }
                render();
            });

            render();
        </script>
    </body>
</html>
