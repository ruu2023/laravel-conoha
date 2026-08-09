import { Fragment, useEffect, useState } from "react";
import { Head } from "@inertiajs/react";
import { BrainCircuit, RefreshCw, Sparkles, Zap } from "lucide-react";
import { useFavicon } from "@/lib/use-favicon";

// ずんだもんNEWS(zundamon)と同じAI要約データを読む。
// .github/workflows/daily-news.ymlがConoHa WINGへ直接同期している。
const DATA_URL = "/data/zundamon/data.json";

type NewsItem = {
    id: string;
    title: string;
    url: string;
    source: string;
    summary: string;
};

function SummaryContent({ text }: { text: string }) {
    if (!text) return null;

    // 「要するに：」以降を抽出して強調
    const summaryParts = text.split(/要するに[：:]/);
    const mainList = summaryParts[0];
    const conclusion = summaryParts[1] ? `要するに：${summaryParts[1]}` : "";

    // 「1. 」「2. 」などの数字付きリストを分割
    const listItems = mainList.split(/\n?\d+\.\s+/).filter((item) => item.trim() !== "");

    return (
        <Fragment>
            <ul className="space-y-3 mb-5">
                {listItems.map((item, i) => (
                    <li key={i} className="flex items-start gap-3 text-slate-700">
                        <span className="text-sm md:text-base leading-relaxed">{item.trim()}</span>
                    </li>
                ))}
            </ul>
            {conclusion && (
                <div className="mt-4 pt-4 border-t border-indigo-100 font-bold text-slate-800 bg-white/50 p-3 rounded-lg ring-1 ring-indigo-50 leading-relaxed italic">
                    <Zap className="inline-block w-4 h-4 text-amber-500 mr-1 mb-1" />
                    {conclusion.trim()}
                </div>
            )}
        </Fragment>
    );
}

export default function Index() {
    useFavicon("/techpulse-favicon.png");

    const [items, setItems] = useState<NewsItem[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(false);

    const loadNews = async () => {
        setLoading(true);
        setError(false);
        try {
            const res = await fetch(`${DATA_URL}?t=${Date.now()}`);
            const data = await res.json();
            setItems(data);
        } catch {
            setError(true);
        }
        setLoading(false);
    };

    useEffect(() => {
        loadNews();
    }, []);

    return (
        <>
            <Head title="AI Tech Pulse" />
            <div className="bg-slate-50 text-slate-900 min-h-screen pb-12">
                <header className="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
                    <div className="max-w-3xl mx-auto px-6 py-4 flex justify-between items-center">
                        <div className="flex items-center gap-2">
                            <Sparkles className="text-indigo-600 w-5 h-5" />
                            <h1 className="text-lg font-bold tracking-tight text-slate-800">AI Tech Pulse</h1>
                        </div>
                        <button onClick={loadNews} className="text-slate-400 hover:text-indigo-600 transition-colors">
                            <RefreshCw className={`w-5 h-5 ${loading ? "animate-spin" : ""}`} />
                        </button>
                    </div>
                </header>

                <main className="max-w-3xl mx-auto px-6 mt-8">
                    <div className="space-y-8">
                        {loading && (
                            <div className="py-20 text-center text-slate-400 animate-pulse font-medium">
                                AIが最新情報を解析中...
                            </div>
                        )}

                        {!loading && error && (
                            <div className="p-8 text-center text-red-500">取得失敗。パスを確認してください。</div>
                        )}

                        {!loading &&
                            !error &&
                            items.map((item) => (
                                <article
                                    key={item.id}
                                    className="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl hover:border-indigo-200 transition-all duration-300"
                                >
                                    <div className="p-6 md:p-10">
                                        <div className="flex justify-between items-start gap-4 mb-6">
                                            <h2 className="text-xl md:text-2xl font-black leading-tight flex-1 tracking-tight text-slate-800">
                                                <a
                                                    href={item.url}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="hover:underline decoration-indigo-200 underline-offset-4"
                                                >
                                                    {item.title}
                                                </a>
                                            </h2>
                                            <span className="text-[10px] font-black px-3 py-1 bg-slate-100 text-slate-400 rounded-lg tracking-widest border border-slate-200">
                                                {item.source}
                                            </span>
                                        </div>

                                        <div className="bg-gradient-to-br from-indigo-50/30 to-slate-50/30 rounded-2xl p-2 md:p-8 border border-indigo-50/50">
                                            <div className="flex items-center gap-2 text-indigo-500 font-black text-[10px] mb-4 uppercase tracking-[0.2em]">
                                                <BrainCircuit className="w-4 h-4" />
                                                <span>AI Summary Insight</span>
                                            </div>

                                            <SummaryContent text={item.summary} />
                                        </div>
                                    </div>
                                </article>
                            ))}
                    </div>
                </main>
            </div>
        </>
    );
}
