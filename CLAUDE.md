# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

A single Laravel app that serves many independent "mini apps" as different paths under `ruu-dev.com` (e.g. `ruu-dev.com/memo`, `ruu-dev.com/imagecrop`), all from one ConoHa WING origin. Most mini apps are small, fully self-contained Blade pages (own `<head>`, inline `<style>`/`<script>`, no shared layout). One app — the apex `root` app, served at `ruu-dev.com/` with no path prefix — is built with Inertia.js + React instead.

(Mini apps used to each live on their own subdomain, e.g. `memo.ruu-dev.com`; this was migrated to path-based routing for SEO/domain-authority consolidation and lower management overhead. The old subdomains are still wired up at the DNS/Cloudflare level but are no longer the canonical URLs and aren't actively maintained — see [docs/subdomain-routing.md](docs/subdomain-routing.md) for the history.)

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

**Visiting a mini app locally**: `http://root.localhost/{app}` (e.g. `http://root.localhost/memo`) — everything is one local host now, matching production's single-domain, path-based routing; the app name is just the first path segment. The apex `root` app itself has no prefix: `http://root.localhost/`.

**Vite/Inertia dev-mode gotcha**: `resources/views/app.blade.php` must have `@viteReactRefresh` immediately before `@vite(...)`. Without it, every React module fails silently (no console error) with `@vitejs/plugin-react can't detect preamble` the moment `resolvePageComponent` tries to import a page — because Laravel's Blade-rendered HTML never goes through Vite's own `index.html` transform, so the usual auto-injected React-refresh preamble never happens on its own. If pages render blank in dev with no errors, check this first, then check `rm -rf node_modules/.vite` (a stale glob-resolution cache from before a `Pages/*.tsx` file existed can persist across `npm run dev` restarts and make `import.meta.glob(...)` resolve to an empty object).

## Architecture: path-based app routing

Full design rationale and history (including the prior subdomain-based scheme and why it was migrated away from) lives in [docs/subdomain-routing.md](docs/subdomain-routing.md) — read it before changing anything in this section, especially before "optimizing" `routes/web.php` to load only the matched app's routes at boot (already tried, already reverted, the doc explains why).

- Every mini app is registered under a `Route::prefix($app)` group matching its name, and that prefix **is** the real public URL (e.g. `ruu-dev.com/memo/...`) — there's no rewriting between what the browser sends and what gets routed, and no middleware involved in routing at all. `routes/web.php` globs `routes/apps/*.php` directly (the filesystem is the only source of truth, no separate whitelist to keep in sync) and registers each app's routes under `Route::prefix($app)`, except `root` which is registered with no prefix (it's the apex app, served at `/`).
- Code that needs to know "which app is this" (the Google-login flow, currently) just hardcodes the app name or route name it means — there's no generic "current app" concept anymore, since with plain path-based routing there was nothing left for it to do beyond what `route()`/`Route::currentRouteName()` already give you.
- **Adding a mini app**: create `routes/apps/{name}.php`, add views/controllers, commit and push. Nothing else to register.
- **Taking an app offline**: add its name to `config/apps.php`'s `disabled` array (version-controlled, no SSH needed) or `DISABLED_APPS` in `.env` (SSH-only emergency toggle). Either way the app's routes never get registered, so it 404s cleanly; nothing else is affected.
- All apps share one session cookie (single domain, no per-app isolation) — this was a deliberate simplification made during the subdomain→path migration, not an oversight.
- The shared Google-login hub is the `login` app (`routes/apps/login.php`, `ruu-dev.com/login`) — gates `techpulse`/`zundamon` behind a hand-picked allow-list (`config/restricted_apps.php`). Renamed from `laravel` during the path migration since "laravel" read as the framework, not an app name.

## Architecture: the `root` app (Inertia + React)

`routes/apps/root.php` renders the `Root/Welcome` Inertia page (the LP) instead of a Blade view.

- `resources/js/app.tsx` — Inertia bootstrap (`createInertiaApp` + `resolvePageComponent`, glob-resolving `./Pages/**/*.tsx`).
- `resources/js/components/ui/*`, `resources/js/lib/utils.ts` — shared shadcn-style primitives (Button/Card/Badge + `cn()` helper), kept outside any per-app namespace since they're meant to be reused by any future Inertia mini app.
- `resources/js/Pages/{AppName}/*` — page content is namespaced per mini app (currently only `Root/`), mirroring the `routes/apps/{name}.php` isolation convention. A second Inertia app later gets `Pages/{Name}/...` without colliding with this one.
- `resources/data/posts.json` is read server-side in `routes/apps/root.php` and passed as an Inertia prop — not imported into the JS bundle — so editing dashboard content doesn't require a Vite rebuild.
- `resources/css/app.css` holds Tailwind v4 config plus the shadcn color-token `@theme`/`:root`/`.dark` block; it's the one shared CSS entry for the whole project (safe to extend in place since nothing else used it before Inertia).

## Deploy pipeline

ConoHa WING has **no Node.js**, so `.github/workflows/deploy.yml` has two jobs:

1. `build-assets` — runs on the Actions runner: checkout, `npm ci && npm run build` (builds `public/build`, including the Inertia/React bundle), then `appleboy/scp-action` ships `public/build/*` to the server.
2. `deploy` (needs `build-assets`) — SSHes in, `git pull`, `composer install --no-dev`, artisan `config:cache`/`route:cache`/`view:cache`, then symlinks everything under `~/laravel-conoha/public/*` (plus the already-transferred `public/build`) into the `~/public_html/ruu-dev.com` docroot (the apex — same Laravel app, same `index.php`). Since the migration to path-based routing this is the only docroot deploy touches; the old `~/public_html/laravel.ruu-dev.com` docroot (and the DNS/Worker setup feeding it, for the now-unmaintained `*.ruu-dev.com` subdomains) is left in place on the server but no longer synced — old subdomain URLs mostly won't resolve to the right content anymore, they were deliberately not redirected.

`composer.lock` and `package-lock.json` are both committed; `public/build` is gitignored (CI-built only, never committed).
