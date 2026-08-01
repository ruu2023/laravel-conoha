import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createRoot } from "react-dom/client";
import type { ComponentType } from "react";

createInertiaApp({
    resolve: async (name) => {
        const module = await resolvePageComponent<{ default: ComponentType }>(
            `./Pages/${name}.tsx`,
            import.meta.glob<{ default: ComponentType }>("./Pages/**/*.tsx"),
        );

        return module.default;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
