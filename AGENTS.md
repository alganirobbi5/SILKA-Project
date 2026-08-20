# AGENTS.md

SILKA Finance is a standalone Laravel 8 / PHP 7.4 back office (Blade views, session auth) for income/expense tracking, COA balances, reports, annual targets, and user management. UI text is **Bahasa Indonesia**.

## Commands

- Run all tests: `vendor\bin\phpunit` (not `php artisan test`).
- Run one suite/filter: `vendor\bin\phpunit --filter AuthTest` or `--filter TransaksiSaldoTest`.
- Tests use a **separate DB `silka_test`** (see `phpunit.xml`), `RefreshDatabase`, root/no password. The DB must exist; create once: `mysql -u root -e "CREATE DATABASE IF NOT EXISTS silka_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"`.
- Assets: Laravel Mix 5 (`npm run dev`, `npm run watch`). No lint/typecheck step; only StyleCI preset is declared (`.styleci.yml`).
- Avoid running `composer install/update`: `composer.lock` targets `php ^7.3` and platform mismatches break dev packages. Vendor is already installed.

## Database & migrations (read before touching schema)

- Core tables (`cluster`, `coa`, `kategori`, `transaksi`, `target_capaians`, `users`) were created **outside Laravel** on the existing DB. Repo migrations are compatibility patches.
- Migrations are idempotent (`Schema::hasTable`/`hasColumn` guards) and deliberately conditional — keep new migrations guarded the same way. **Never** use `migrate:fresh`, `TRUNCATE`, or data-dropping migrations; this is a financial system.
- Dev DB name is `silka` (`.env`); README mentions a legacy `silka.` name — trust `.env`.
- Monetary columns are `DECIMAL(18,2)`. `transaksi.jenis` is canonical lowercase `enum('pemasukan','pengeluaran')`. Negative balances are allowed by design.
- Default category id=1 `Tanpa Kategori` must always exist; deleting a category reassigns its transactions to id=1 inside a DB transaction.

## Architecture

- Financial integrity is the core invariant: any transaction must be mirrored in `coa.saldo`. All create/edit/delete of transactions goes through `app/Services/TransaksiSaldoService.php` (atomic `DB::transaction` + `lockForUpdate`, edit locks COAs in ascending id order). Do not add controller routes that mutate balances directly.
- Reports (web/print/PDF/Excel) share one dataset source: `app/Services/ReportService.php`. `.xlsx` export is hand-rolled OOXML/ZipArchive (no PhpSpreadsheet) and sanitizes `= + - @` formula injection.
- PDFs are rendered by `app/Support/PdfRenderer.php` (dompdf): registers optional `storage/fonts/segoe-ui/*.ttf`, forces 1GB memory limit, `isRemoteEnabled=false`. Run `php artisan storage:link` if uploads/PDF fonts are missing.
- Auth: session-based; login throttled `5,1`; all internal routes behind `auth`. `users.level` is `enum('admin','bendahara')`; only `admin` passes the `manage-users` Gate (`app/Providers/AuthServiceProvider.php`), which guards the `users` resource routes.

## Conventions

- Keep UI strings, flash messages, and tests' method names in Bahasa Indonesia; keep code identifiers in English.
- Money formatting uses the `rupiah()` helper in `app/helpers.php` (`Rp1.234.567,89`) — never format money inline.
- Test files: one per domain area in `tests/Feature/` (`AuthTest`, `TransaksiSaldoTest`, `KategoriCoaTest`, `DashboardTargetTest`, `LaporanTest`, `UserTest`, `PageRenderTest`). Verify balance math with tests when touching `TransaksiSaldoService`.

## Docs drift

`README.md` still describes a user photo-profile upload feature; that column was dropped (migration `2026_08_18_000012`) and the feature no longer exists in code. Do not resurrect it.
