import { PostCard } from "./components/post-card";
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

export default function Dashboard({ posts }: { posts: Post[] }) {
    return (
        <div className="max-w-7xl mx-auto py-10 space-y-8 p-6">
            <header className="flex justify-between items-center">
                <h1 className="text-4xl font-bold tracking-tight">100 Days Roadmap</h1>
            </header>

            <div className="flex flex-col gap-2">
                <p className="text-muted-foreground text-lg">
                    学習とアウトプットの軌跡。
                </p>
            </div>

            <TechStackChart />

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                {posts.map((post) => (
                    <PostCard key={post.day} post={post} />
                ))}
            </div>
        </div>
    );
}
