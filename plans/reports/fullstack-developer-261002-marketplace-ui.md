# Report: public UI refresh + SAP B1 marketplace

Worktree: `E:\Workplace\Projects\webcuatui\.claude\worktrees\agent-aad2b44bc535b0ec7`, branch `feat/marketplace-ui-impl` (renamed from auto worktree branch). 6 commits on top of 2851690. Not pushed/merged/deployed. (Report lives in the worktree `plans/reports/` because writes to the shared checkout path were blocked; copy to `E:\Workplace\Projects\webcuatui\plans\reports\` if wanted.)

## Result
- `npm run build` OK. `php artisan test`: 31 passed (139 assertions), SQLite in-memory. Pint run on all changed PHP.
- Browser check (Playwright + Chromium, desktop 1280 / mobile 390, dark + light): `/`, `/marketplace`, `?integration=Shopify`, addon page, `/tools`, `/blogs`, `/blogs/{slug}`, `/shop`, 404. No console errors, no horizontal overflow. Only warnings: Chative chat widget preload (third party). Verified: mobile menu, theme toggle (persists), home contact form (JSON /api/contact), quote form invalid + valid + success state, blog share modal (Alpine).

## Defaults used (as instructed)
Dark terminal/GitHub look kept, now tokenised (light/dark via `:root`/`.dark` vars exposed in Tailwind v4 `@theme inline`; default theme = dark, toggle persists). Vietnamese only. VAS = Vietnamese Accounting Standards reporting. All 6 seeded addons `billing=quote`, `price=0`, no prices published. No customers/benchmarks/certs.

## Done
- Cleanup: removed unused views/components (old `components/{home,layouts,nav,articles,ui}`, Breeze `auth/*` views + Breeze form components, `partials/{about,skills,projects}`, `layouts/main`). Grep-verified no references; `routes/auth.php` not loaded. Single layout `layouts/app` + `partials/{navbar,footer}`.
- Stale Breeze tests deleted: `tests/Feature/Auth/*`, `tests/Feature/ProfileTest.php` (routes/auth.php never loaded, app uses Filament auth).
- `blogs::getDataArray`: thumbnail null when no cover (was always `asset('storage/')`), stars = 0 instead of `rand()`, author avatar null (views show initial) instead of github shadcn placeholder. Blog list/show/home views updated. Slugs/URLs unchanged.
- `/test` route removed. README rewritten (Laravel 13 + Blade + Livewire 4 + Filament 5, setup, tests).
- Navbar Marketplace / Tools / Blog / Liên hệ (vanilla JS, no Alpine/Livewire needed). New home: hero, featured addons, tools strip, latest posts (still cached 10 min; featured addons cached 10 min too), contact. Home loads no AdSense, no Livewire (tested). AdSense now only on blog post pages. Fonts: dropped bunny.net + Google Fonts, system font stacks.
- Marketplace: migration `2026_10_02_000001_add_addon_fields_to_products_table` (type, integration, billing, summary, sap_versions, db_support, features, data_flow, faqs, gallery, docs_url, demo_url; all nullable/defaulted). `Product` model with scopes/constants. `AddonSeeder` (idempotent, called from `DatabaseSeeder`). `/marketplace` (filters integration + SAP version via links, unknown values ignored), `/marketplace/{slug}` (overview, features, data flow, compatibility, FAQ, price box, quote form, related), JSON-LD `SoftwareApplication`+`Product` (offer only when real price), ItemList on listing, canonical per integration.
- Quote form: POST `/marketplace/{slug}/quote` (throttle 6/min, honeypot), stored as `Contacts` (topic = slug, reason "Yêu cầu báo giá addon"), reuses existing notification + autoreply mails via new `ContactController::recordInquiry` (API `store` now uses it; mail failures are logged, contact still saved).
- Sitemap: `/sitemap.xml` dynamic (static pages, addons, shop products, published posts); `SitemapController::generateSitemap()` writes same content to `public/sitemap.xml`. `app:generate-sitemap` command untouched (not in my file list; it still crawls, which now finds marketplace via navbar).
- `/tools` landing with Tax tool card linking `/customer`.
- Filament `ProductResource`: tabs General / Addon (type, integration, billing, SAP versions, DB, summary, features, data flow, FAQ, gallery, docs/demo URL), type/integration filters, columns.
- `/shop` kept working; now limited to `type=physical` so addons do not show as cart items.
- Tests: `MarketplaceTest`, `SitemapTest`, `PublicPagesTest`, `ProductAdminTest`.

## Deviations / notes
- `db_support` stored as JSON array (allows both SQL Server + HANA), plan said single value.
- Extra columns beyond plan: `summary`, `data_flow`, `faqs` (needed for the product page sections).
- `Product::SAP_VERSIONS = ['9.3', '10.0']` and seeded support (both versions, SQL Server + HANA) are placeholders, NOT verified claims. Owner must confirm per addon before launch. Seeded feature lists are generic scope descriptions, also to confirm.
- Blog show page needed Alpine (share modal) but never loaded it when Livewire absent; now loads Alpine 3.14.9 from jsDelivr on that page only. Shop pages still use their own gray/white palette (functional, not re-skinned).
- Cart icon removed from navbar (spec lists 4 items); shop linked from footer.
- Blog list/show still use legacy `gh-*` utilities (kept as aliases of the tokens).
- Environment: `php`/`composer` not on Git Bash PATH; used ServBay `php.exe` directly (no PowerShell tool available to me). Worktree has an untracked local `.env` copied from `.env.example` (gitignored) so tests run without a dotenv warning. package-lock.json untouched.
- Not done (out of scope): font self-hosting/AVIF `srcset`, Cloudflare cache, Lighthouse measurement, English copy, phase-2 licensing, deploy.

## Unresolved questions
1. Which SAP B1 versions / DBs does each addon really support, and which e-invoice / bank / Magento versions? (seed data is placeholder)
2. Real "Tax tool" entry URL: currently `/customer`; confirm once the other agent lands it (and whether it should show "Cần đăng nhập").
3. Keep chat widget (Chative) on every page? It causes preload warnings and extra requests; left as before.
4. Should `public/sitemap.xml` (static, tracked) be regenerated/removed so the dynamic `/sitemap.xml` route is not shadowed on the server?
5. Brand: still "HarryDev" + `logo.jpg`; any fixed brand colours/logo for the marketplace?
