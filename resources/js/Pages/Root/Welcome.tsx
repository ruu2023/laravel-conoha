import { useState } from "react";
import { Link } from "@inertiajs/react";
import { Button } from "@/components/ui/button";
import { TechStackChart } from "./components/tech-stack-chart";

type Post = {
    day: number;
    date: string;
    title: string;
    description?: string;
    features: string[];
    tags: string[];
    demoLink?: string;
    url: string;
};

type FeaturedApp = {
    name: string;
    desc: string;
    href: string;
    accent: string;
    tint: string;
    mono: string;
    image: string;
};

// 実際に完成・公開済みのアプリのみ。dockerfilesはまだ「準備中」のスタブなので
// 完成したら追加する。techpulse/zundamonはGoogleログイン制限があり、未許可の
// 訪問者を403に誘導してしまうためLPには載せない。
const FEATURED_APPS: FeaturedApp[] = [
    {
        name: "画像切り抜きツール",
        desc: "アップロード不要、ブラウザ内だけで完結する画像クロップツール。",
        href: "/imagecrop",
        accent: "#2563eb",
        tint: "#eff6ff",
        mono: "IC",
        image: "/imagecrop-preview.png",
    },
    {
        name: "Universal Draft App",
        desc: "文字数制限を自由に設定できる、ブラウザ自動保存メモ。",
        href: "/memo",
        accent: "#b8262c",
        tint: "#fef2f2",
        mono: "MD",
        image: "/memo-preview.png",
    },
];

// ヒーローのスマホ重ね表示は装飾目的なのでリンクなし(クリック可能な導線は
// 下のFeatured Appsに任せる)。FEATURED_APPSと同じ2つの実アプリだけを使い、
// techpulse/zundamon(Googleログイン制限あり)は載せない。
const HERO_POSITIONS = [
    { left: "40px", top: "30px", rot: "-6deg", dur: "5s", delay: "0s", z: 2 },
    { left: "190px", top: "0px", rot: "5deg", dur: "6s", delay: "0.4s", z: 3 },
];

function FeaturedAppCard({ app }: { app: FeaturedApp }) {
    return (
        <Link
            href={app.href}
            className="group flex flex-col items-center gap-4 rounded-3xl p-5 text-center no-underline transition-all duration-200 hover:-translate-y-1"
            style={{ backgroundColor: app.tint }}
        >
            <div
                className="w-36 -rotate-2 overflow-hidden rounded-[20px] border-4 border-white bg-white shadow-lg transition-transform duration-200 group-hover:rotate-0"
                style={{ boxShadow: `0 16px 30px -14px ${app.accent}66` }}
            >
                <img
                    src={app.image}
                    alt={`${app.name}のスクリーンショット`}
                    className="aspect-[3/5] w-full object-cover object-top"
                />
            </div>
            <div>
                <div className="mb-1 text-base font-bold" style={{ color: app.accent }}>
                    {app.name}
                </div>
                <div className="text-xs leading-relaxed text-neutral-600">{app.desc}</div>
            </div>
            <div
                className="mt-auto inline-flex items-center gap-1.5 rounded-full px-5 py-2 text-xs font-bold text-white"
                style={{ backgroundColor: app.accent }}
            >
                試してみる →
            </div>
        </Link>
    );
}

function PostGridCard({ post }: { post: Post }) {
    const accent = post.day % 2 === 0 ? "#2563eb" : "#7c3aed";
    return (
        <a
            href={post.url}
            target="_blank"
            rel="noreferrer"
            className="flex flex-col gap-2.5 rounded-2xl border border-black/10 bg-white p-4 no-underline transition-all duration-150 hover:-translate-y-0.5 hover:shadow-[0_10px_20px_-10px_rgba(20,20,30,0.18)]"
        >
            <div
                className="flex size-9 items-center justify-center rounded-[10px] text-xs font-bold text-white"
                style={{ backgroundColor: accent }}
            >
                {String(post.day).padStart(3, "0")}
            </div>
            <div className="line-clamp-1 text-[13.5px] font-bold text-neutral-900">{post.title}</div>
            <div className="line-clamp-2 text-xs leading-snug text-neutral-500">
                {post.description || "制作の詳細はXをチェック"}
            </div>
            <div className="mt-auto text-xs font-semibold" style={{ color: accent }}>
                見る →
            </div>
        </a>
    );
}

export default function Welcome({ posts }: { posts: Post[] }) {
    const [appsShown, setAppsShown] = useState(20);
    const visiblePosts = posts.slice(0, appsShown);
    const hasMoreApps = appsShown < posts.length;

    return (
        <div className="relative min-h-screen overflow-x-hidden bg-[oklch(99%_0.003_250)] text-[oklch(22%_0.02_260)]">
            {/* Header */}
            <header className="sticky top-0 z-40 border-b border-[oklch(90%_0.005_260)] bg-[oklch(99%_0.003_250)]/85 backdrop-blur-md">
                <div className="mx-auto flex max-w-[1200px] items-center justify-between px-6 py-4">
                    <a href="#top" className="flex items-center gap-2.5 text-base font-bold tracking-tight text-inherit no-underline">
                        <span className="flex size-[30px] items-center justify-center rounded-[9px] bg-gradient-to-br from-[#2563eb] to-[#7c3aed] text-[11px] font-extrabold text-white">
                            100
                        </span>
                        100 Days Challenge
                    </a>
                    <div className="flex items-center gap-6">
                        <nav className="hidden gap-8 text-sm font-medium text-neutral-500 sm:flex">
                            <a href="#top" className="text-inherit no-underline hover:text-neutral-900">
                                ホーム
                            </a>
                            <a href="#all-apps" className="text-inherit no-underline hover:text-neutral-900">
                                すべてのアプリ
                            </a>
                            <a href="#about" className="text-inherit no-underline hover:text-neutral-900">
                                チャレンジについて
                            </a>
                        </nav>
                        <a
                            href="https://x.com/ruu_web"
                            target="_blank"
                            rel="noreferrer"
                            aria-label="X (旧Twitter)"
                            className="flex size-8 items-center justify-center rounded-full text-neutral-500 transition-colors hover:bg-neutral-100 hover:text-neutral-900"
                        >
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </header>

            {/* Hero */}
            <section
                id="top"
                className="mx-auto grid max-w-[1200px] items-center gap-12 px-6 pb-14 pt-20 md:grid-cols-[1.1fr_0.9fr] md:pt-[88px]"
                style={{
                    background:
                        "radial-gradient(ellipse 900px 500px at 20% 0%, oklch(96% 0.02 280 / 0.6), transparent)",
                    animation: "lpFadeUp 0.7s ease both",
                }}
            >
                <div>
                    <div className="mb-5 inline-flex items-center gap-2 rounded-full bg-[oklch(96%_0.015_250)] px-3.5 py-1.5 text-[13px] font-semibold text-[oklch(45%_0.12_250)]">
                        個人開発チャレンジ · Day 100/100
                    </div>
                    <h1 className="m-0 mb-5 text-[40px] font-extrabold leading-[1.15] tracking-tight md:text-[52px]">
                        100日で100個の
                        <br />
                        アプリを作りました。
                    </h1>
                    <p className="m-0 mb-8 max-w-[480px] text-lg leading-relaxed text-neutral-500">
                        アイデアを形にする毎日。
                        <br />
                        その中から、実際に使ってほしいアプリを公開しています。
                    </p>
                    <a
                        href="#featured"
                        className="inline-flex items-center gap-2.5 rounded-full bg-gradient-to-br from-[#2563eb] to-[#7c3aed] px-6.5 py-3.5 text-[15px] font-semibold text-white no-underline transition-all duration-150 hover:-translate-y-0.5 hover:shadow-[0_12px_24px_-8px_rgba(20,20,30,0.35)]"
                    >
                        ▶ 今すぐアプリを試してみる
                    </a>
                    <div className="mt-12 flex gap-8">
                        <div>
                            <div className="text-2xl font-extrabold text-[#2563eb]">100</div>
                            <div className="text-[13px] text-neutral-500">Apps</div>
                        </div>
                        <div>
                            <div className="text-2xl font-extrabold text-[#7c3aed]">100</div>
                            <div className="text-[13px] text-neutral-500">Days</div>
                        </div>
                        <div>
                            <div className="text-2xl font-extrabold text-[#5b3fc0]">∞</div>
                            <div className="text-[13px] text-neutral-500">Ideas</div>
                        </div>
                    </div>
                </div>

                <div className="relative hidden h-[460px] md:block">
                    {FEATURED_APPS.map((app, i) => (
                        <div
                            key={app.href}
                            className="absolute w-[190px] overflow-hidden rounded-[26px] border-[6px] border-white bg-white shadow-[0_20px_40px_-16px_rgba(20,20,30,0.3)]"
                            style={{
                                left: HERO_POSITIONS[i].left,
                                top: HERO_POSITIONS[i].top,
                                zIndex: HERO_POSITIONS[i].z,
                                // @ts-expect-error custom property consumed by lpFloatY
                                "--r": HERO_POSITIONS[i].rot,
                                animation: `lpFloatY ${HERO_POSITIONS[i].dur} ease-in-out infinite`,
                                animationDelay: HERO_POSITIONS[i].delay,
                            }}
                        >
                            <img
                                src={app.image}
                                alt={`${app.name}のスクリーンショット`}
                                className="aspect-[3/5] w-full object-cover object-top"
                            />
                        </div>
                    ))}
                </div>
            </section>

            {/* Featured Apps */}
            <section id="featured" className="mx-auto max-w-[1200px] px-6 pb-6 pt-16">
                <div className="mb-11 text-center">
                    <h2 className="m-0 mb-3 text-[34px] font-extrabold tracking-tight">🚀 おすすめアプリ</h2>
                    <p className="m-0 text-base text-neutral-500">特に使ってほしいアプリをピックアップしました。</p>
                </div>
                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {FEATURED_APPS.map((app) => (
                        <FeaturedAppCard key={app.href} app={app} />
                    ))}
                </div>
            </section>

            {/* All Apps preview */}
            <section id="all-apps" className="mx-auto max-w-[1200px] px-6 pb-6 pt-20">
                <div className="mb-10 text-center">
                    <h2 className="m-0 mb-3 text-[30px] font-extrabold tracking-tight">All Apps</h2>
                    <p className="m-0 text-[15px] text-neutral-500">100日間の制作ログから、最近の取り組みを一部紹介。</p>
                </div>
                <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5">
                    {visiblePosts.map((post) => (
                        <PostGridCard key={post.day} post={post} />
                    ))}
                </div>
                {hasMoreApps && (
                    <div className="mt-7 flex justify-center">
                        <Button
                            variant="outline"
                            size="lg"
                            className="rounded-full"
                            onClick={() => setAppsShown((n) => n + 100)}
                        >
                            他のアプリも見る(全{posts.length}個) ↓
                        </Button>
                    </div>
                )}
            </section>

            {/* About */}
            <section id="about" className="mx-auto grid max-w-[1200px] items-center gap-14 px-6 py-24 md:grid-cols-[0.8fr_1.2fr]">
                <div className="h-[280px] overflow-hidden rounded-3xl bg-gradient-to-br from-[#2563eb]/10 via-[#7c3aed]/10 to-[#b8262c]/10">
                    <img
                        src="/about-illustration.png"
                        alt="開発者のイラスト"
                        className="size-full object-cover"
                    />
                </div>
                <div>
                    <h2 className="m-0 mb-5 text-[28px] font-extrabold tracking-tight">100日チャレンジについて</h2>
                    <p className="m-0 mb-3.5 text-base leading-[1.9] text-neutral-600">
                        毎日ひとつ、アイデアを思いついたらすぐに形にする。完璧じゃなくてもいい。まず作ってみる。
                    </p>
                    <p className="m-0 mb-3.5 text-base leading-[1.9] text-neutral-600">
                        便利なもの、ニッチなもの、実験的なもの、失敗したもの。100個作ってみることで、どんなアプリが本当に使われるのかを探しました。
                    </p>
                    <p className="m-0 text-base leading-[1.9] text-neutral-600">このチャレンジは、まだ続いています。</p>
                </div>
            </section>

            <section className="mx-auto max-w-[1200px] px-6 pb-24">
                <div className="mb-10 text-center">
                    <h2 className="m-0 mb-3 text-[30px] font-extrabold tracking-tight">🛠️ 100日間の使用技術</h2>
                    <p className="m-0 text-[15px] text-neutral-500">GitHubリポジトリの言語構成から見る、100日間で触った技術たち。</p>
                </div>
                <div className="rounded-3xl border border-black/10 bg-white p-6 sm:p-8">
                    <TechStackChart />
                </div>
            </section>

            {/* Footer */}
            <footer className="border-t border-[oklch(90%_0.005_260)] px-6 py-10">
                <div className="mx-auto flex max-w-[1200px] flex-col items-center justify-between gap-2 text-[13px] text-neutral-500 sm:flex-row">
                    <div>© 2026 ruu2023 · 100 Days Challenge</div>
                    <div>Made one app at a time.</div>
                </div>
            </footer>
        </div>
    );
}
