# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

A single Laravel app that serves many independent "mini apps" on different subdomains of `ruu-dev.com` (e.g. `memo.ruu-dev.com`, `imagecrop.ruu-dev.com`) plus the apex domain `ruu-dev.com` itself, all from one ConoHa WING origin. Most mini apps are small, fully self-contained Blade pages (own `<head>`, inline `<style>`/`<script>`, no shared layout). One app — the apex `root` app — is built with Inertia.js + React instead.

## Commands

Local development runs via Laravel Sail (Docker). The app container is `laravel-conoha-laravel.test-1`; a `mysql` container backs it.

```bash
./vendor/bin/sail up -d              # start the stack (if not already running)
./vendor/bin/sail artisan test       # run the full test suite
./vendor/bin/sail artisan test --filter=test_name_or_class   # run a single test
./vendor/bin/sail composer test      # same as above, plus config:clear first
./vendor/bin/sail npm run dev        # Vite dev server (HMR) — see gotcha below
./vendor/bin/sail npm run build      # production build -> public/build
./vendor/bin/pint                    # PHP code style (Laravel Pint)
```

Alias `sail` to `./vendor/bin/sail` in your shell profile to drop the prefix (Laravel's own convention).

**Visiting a mini app locally**: `http://{app}.localhost` (e.g. `http://memo.localhost`) — no `/etc/hosts` edit needed, `*.localhost` resolves to loopback. The apex/root app is the one exception with no natural subdomain of its own; visit it locally at `http://root.localhost`, which works via the same `*.localhost` convenience matching in `ResolveAppSubdomain` since `root` is a registered app name.

**Vite/Inertia dev-mode gotcha**: `resources/views/app.blade.php` must have `@viteReactRefresh` immediately before `@vite(...)`. Without it, every React module fails silently (no console error) with `@vitejs/plugin-react can't detect preamble` the moment `resolvePageComponent` tries to import a page — because Laravel's Blade-rendered HTML never goes through Vite's own `index.html` transform, so the usual auto-injected React-refresh preamble never happens on its own. If pages render blank in dev with no errors, check this first, then check `rm -rf node_modules/.vite` (a stale glob-resolution cache from before a `Pages/*.tsx` file existed can persist across `npm run dev` restarts and make `import.meta.glob(...)` resolve to an empty object).

## Architecture: subdomain routing

Full design rationale and history of past mistakes lives in [docs/subdomain-routing.md](docs/subdomain-routing.md) — read it before changing anything in this section, especially before "optimizing" `ResolveAppSubdomain` to load only the matched app's routes at boot (already tried, already reverted, the doc explains why).

- DNS: `*.ruu-dev.com` is a wildcard pointing at ConoHa WING. A Cloudflare Worker in front rewrites `Host` to `laravel.ruu-dev.com` and forwards the real subdomain in an `X-App-Subdomain` header. The apex `ruu-dev.com` is **not** covered by that wildcard — it has its own DNS entry, is not rewritten by the Worker, and arrives with `Host: ruu-dev.com` literally.
- `App\Http\Middleware\ResolveAppSubdomain::resolve()` picks the app name, in order: the `X-App-Subdomain` header → (local env only) a `{app}.localhost` Host → literal `Host: ruu-dev.com` → `laravel` (default). It then rewrites the request path with a `/{app}` prefix (via `Request::create`) before routing — this prefix never reaches the browser.
- `routes/web.php` loops over `ResolveAppSubdomain::apps()` (which globs `routes/apps/*.php` — the filesystem is the only source of truth, no separate whitelist to keep in sync) and registers each app's routes under `Route::prefix($app)`.
- **Adding a mini app**: create `routes/apps/{name}.php`, add views/controllers, commit and push. Nothing else to register.
- **Taking an app offline**: add its name to `config/apps.php`'s `disabled` array (version-controlled, no SSH needed) or `DISABLED_APPS` in `.env` (SSH-only emergency toggle). Either way the app 404s cleanly; nothing else is affected.
- `App\Routing\SubdomainAwareUrlGenerator` strips the `/{app}` prefix back out of anything `route()`/`action()` generates (the prefix-stripping only happens on the way in, not the way out, so this exists to fix outgoing URLs) and rewrites `laravel.ruu-dev.com` back to the real subdomain for absolute URLs.
- `App\Http\Middleware\HandleInertiaRequests::urlResolver()` does the equivalent fix for Inertia's page `url` prop, which Inertia derives from the (already-prefixed) current request instead of from `route()`.

## Architecture: the `root` app (Inertia + React)

`routes/apps/root.php` renders two Inertia pages (`Root/Welcome`, `Root/Dashboard`) instead of a Blade view. This is the only Inertia/React consumer in the project so far.

- `resources/js/app.tsx` — Inertia bootstrap (`createInertiaApp` + `resolvePageComponent`, glob-resolving `./Pages/**/*.tsx`).
- `resources/js/components/ui/*`, `resources/js/lib/utils.ts` — shared shadcn-style primitives (Button/Card/Badge + `cn()` helper), kept outside any per-app namespace since they're meant to be reused by any future Inertia mini app.
- `resources/js/Pages/{AppName}/*` — page content is namespaced per mini app (currently only `Root/`), mirroring the `routes/apps/{name}.php` isolation convention. A second Inertia app later gets `Pages/{Name}/...` without colliding with this one.
- `resources/data/posts.json` is read server-side in `routes/apps/root.php` and passed as an Inertia prop — not imported into the JS bundle — so editing dashboard content doesn't require a Vite rebuild.
- `resources/css/app.css` holds Tailwind v4 config plus the shadcn color-token `@theme`/`:root`/`.dark` block; it's the one shared CSS entry for the whole project (safe to extend in place since nothing else used it before Inertia).

## Deploy pipeline

ConoHa WING has **no Node.js**, so `.github/workflows/deploy.yml` has two jobs:

1. `build-assets` — runs on the Actions runner: checkout, `npm ci && npm run build` (builds `public/build`, including the Inertia/React bundle), then `appleboy/scp-action` ships `public/build/*` to the server.
2. `deploy` (needs `build-assets`) — SSHes in, `git pull`, `composer install --no-dev`, artisan `config:cache`/`route:cache`/`view:cache`, then symlinks everything under `~/laravel-conoha/public/*` (plus the already-transferred `public/build`) into **two** docroots: `~/public_html/laravel.ruu-dev.com` (every `*.ruu-dev.com` subdomain, since the Worker always rewrites Host to this) and `~/public_html/ruu-dev.com` (the apex — same Laravel app, same `index.php`, distinguished purely by which docroot nginx picks based on the raw `Host` header).

`composer.lock` and `package-lock.json` are both committed; `public/build` is gitignored (CI-built only, never committed).
