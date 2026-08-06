import { createInertiaApp, router } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createRoot } from "react-dom/client";
import type { ComponentType } from "react";

declare global {
  interface Window {
    gtag?: (...args: unknown[]) => void;
  }
}

router.on("navigate", (event) => {
  if (typeof window.gtag === "function") {
    window.gtag("event", "page_view", {
      page_path: event.detail.page.url,
      page_title: document.title,
    });
  }
});

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
