import { useEffect } from "react";

const DEFAULT_HREF = "/favicon.ico";

/**
 * Swaps the shared #app-favicon <link> (see resources/views/app.blade.php)
 * to a different icon for the lifetime of this component, restoring the
 * default on unmount. Not handled via Inertia's <Head> because Inertia only
 * auto-replaces <title> tags that way — other tags just get appended
 * alongside whatever's already there, not swapped.
 */
export function useFavicon(href: string): void {
    useEffect(() => {
        const link = document.getElementById("app-favicon") as HTMLLinkElement | null;
        if (!link) return;

        link.href = href;

        return () => {
            link.href = DEFAULT_HREF;
        };
    }, [href]);
}
