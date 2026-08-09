import { useState, useEffect } from "react";
import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    ResponsiveContainer,
} from "recharts";

// 型定義
interface LanguageData {
    name: string;
    count: number;
}

// LPの他セクションと見た目を揃えるため、shadcnのCardは使わず素のdivで囲む
// (呼び出し元のWelcome.tsxが見出し・余白・カード枠を持つ)。
export function TechStackChart() {
    const [data, setData] = useState<LanguageData[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchLanguages = async () => {
            try {
                const response = await fetch(
                    "https://api.github.com/repos/ruu2023/100-days-of-code-2026/languages",
                );
                const rawData = (await response.json()) as Record<
                    string,
                    number
                >;

                // GitHub API のレスポンス { "TypeScript": 1234, "Go": 567 } を
                // 配列 [{ name: "TypeScript", count: 1234 }, ...] に変換
                const formattedData = Object.entries(rawData)
                    .map(([name, count]) => ({
                        name,
                        count: count as number,
                    }))
                    // バイト数が多い順に並び替え
                    .sort((a, b) => b.count - a.count);

                setData(formattedData);
            } catch (error) {
                console.error("Error fetching GitHub languages:", error);
            } finally {
                setLoading(false);
            }
        };

        fetchLanguages();
    }, []);

    if (loading) {
        return (
            <div className="flex h-75 w-full items-center justify-center text-sm text-neutral-400">
                読み込み中...
            </div>
        );
    }

    return (
        <div className="h-75 w-full">
            <ResponsiveContainer width="100%" height="100%">
                <BarChart data={data} layout="vertical" margin={{ left: 20, right: 30 }}>
                    <defs>
                        <linearGradient id="techStackBar" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stopColor="#2563eb" />
                            <stop offset="100%" stopColor="#7c3aed" />
                        </linearGradient>
                    </defs>
                    <CartesianGrid strokeDasharray="3 3" horizontal={false} stroke="oklch(90% 0.005 260)" />
                    <XAxis type="number" hide />
                    <YAxis
                        dataKey="name"
                        type="category"
                        width={100}
                        tick={{ fontSize: 12, fill: "oklch(40% 0.02 260)" }}
                        axisLine={false}
                        tickLine={false}
                    />
                    <Tooltip
                        cursor={{ fill: "oklch(96% 0.015 250)" }}
                        contentStyle={{
                            borderRadius: "12px",
                            border: "1px solid oklch(90% 0.005 260)",
                            boxShadow: "0 12px 24px -12px rgba(20,20,30,0.2)",
                            fontSize: "13px",
                        }}
                    />
                    <Bar dataKey="count" fill="url(#techStackBar)" radius={[0, 8, 8, 0]} />
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}
