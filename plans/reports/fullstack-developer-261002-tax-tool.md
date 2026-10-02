# Tax tool implementation report

Branch: `feat/tax-tool-impl` (worktree `.claude/worktrees/agent-ac684204ddd8d45c1`), 4 commits, not pushed.
Tests: `php artisan test --filter=Tax` -> 48 pass (Pest, SQLite memory, `Http::fake`, `Storage::fake`). Pint clean on owned files.
Full suite: 23 failures, all pre-existing (Breeze routes missing, public layout needs Vite manifest). Untouched.

## Delivered
- Data: migrations (companies, company_customer, gdt_sessions, tax_invoices, tax_export_runs, notifications guarded by hasTable); models + `CompanyScope` global scope (Filament tenant); factories.
- Auth/tenancy: customer panel `/customer` with login, registration, tenant `Company` (slug = id), RegisterCompany (owner pivot), EditCompanyProfile (owner only), MST validation + unique per owner. Customer implements FilamentUser/HasTenants.
- Policies: Company (members view, owner update/delete), TaxInvoice (read-only), TaxExportRun. Cross-company URL gives 404.
- GDT: `GdtClient` (Http client, browser headers port, TLS verify ON, 429 backoff, 401 -> GdtUnauthorizedException), `GdtSessionService` (token encrypted + expires_at from JWT exp, 401 drops session, no username/password stored), `InvoiceSearch` (30-day cap), `InvoiceSyncService` (state paging size 50, standard + mtt merged, dedupe, upsert, detail cache).
- Exports: XML single/zip, Excel (4 sheets ported, formula-injection safe), HTML preview (portal HTML inlined, scripts stripped, sandboxed iframe) + fallback layout, print-to-PDF button. Queue job `RunTaxExport` -> `ExportRunner`, DB notification on done/failed, 7-day expiry, max 3000 invoices.
- Limits: `TaxRateLimiter` (per company + per customer for sync/export/connect), one active export per company.
- UI: ConnectGdt page (captcha as img data-URI, password cleared from state), Invoices resource (filters, sync, export actions, bulk export, detail/preview/XML row actions), Export history resource (poll, download, delete).

## Paid-license gate (branch integration/tax-marketplace-ui)
- `tax_licenses` migration, `TaxLicense` model (+ `valid()` scope), factory (`expired()`, `revoked()`), `CompanyFactory::licensed()`.
- `Company`: `taxLicenses()`, `activeLicense()` (valid one with latest expiry, else last-lapsed active), `hasValidLicense()`, `licenseDaysLeft()`, `assertLicensed()` (throws `LicenseRequiredException`, a GdtException, 403).
- Gate points: `GdtSessionService::clientFor` (all portal calls: sync, detail, XML, HTML, export fetches), `ExportRequestService::request`, `ExportRunner::execute` (re-check at run time -> run failed + notification, no portal call), `TaxInvoicePolicy::view`, `TaxInvoiceResource` query empty without license, export download hidden. Open: registration, company creation, ConnectGdt login.
- `LicenseNotice`: Vietnamese blocked message with contact email, banner (red blocked, amber <= 14 days, green otherwise) via panel render hook on dashboard, connect, invoices and export pages. Contact email is a constant copied from `partials/footer.blade.php` (no settings key exists).
- Admin: `TaxLicenseResource` (+ List/Create/Edit pages, nav group Tax): searchable company select (name + MST), starts_at (start of day), expires_at (end of day), status, notes, created_by set on create; table with company, MST, expiry, days left, status badge, filters expiring 30d and expired. `TaxLicensePolicy` allows only `User` (admin); customers denied.
- Tests: `TaxLicenseGateTest` (dataset none/expired/revoked/not started for sync, export, list+policy; valid allows; queued job re-check; renewal; not-started renewal; cross-company; connect still allowed; banner styles + dashboard; admin create/list/filter; customer denied incl. /admin URLs). Existing Tax tests now use `licensed()` companies. Full suite 104 pass; Pint clean.

## Deviations / notes
- No PDF library and no Chromium: no PDF zip. Preview modal has "In / Lưu PDF" (browser print).
- GDT native Excel merge ("BK thuế (GDT)" sheet) not ported; workbook is generated from cached data.
- mtt source failure is a warning, standard source failure is fatal (Electron swallowed both).
- Export file download uses Filament table action on `local` disk (no new route). Expired files are not pruned automatically (no scheduler/console file in ownership).
- QR in HTML uses `bacon/bacon-qr-code`, present only transitively (not in composer.json); skipped silently if absent.
- Added `databaseNotifications()` to the panel; needs `notifications` table (migration included).
- `.claude/settings.local.json` shows modified in the worktree; not committed, not mine.
- Report written inside the worktree (`plans/reports/`), since writes to the shared checkout path were refused.

## Unresolved questions
- Does the portal return `exp` in the JWT? Fallback TTL is 55 min; verify on a real token.
- Real GDT totals vs Electron app not compared; only Http::fake verified.
- Free vs paid tool / company limit and open vs approved registration still undecided (registration is open).
- Should expired export files be pruned by a scheduled command? Needs a console file outside my ownership.
- `bacon/bacon-qr-code` should be required explicitly in composer.json (outside my ownership).
- Production queue worker must run for exports (queue driver `database`).
