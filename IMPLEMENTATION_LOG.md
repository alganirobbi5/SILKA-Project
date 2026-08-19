# SILKA Implementation Log

## Baseline
- Commit/branch awal: `main` (3c41645 "first commit")
- Laravel/PHP: Laravel 8.83.29 / PHP 8.3.30 CLI (composer lock ditulis untuk `php ^7.3`; vendor sudah terpasang dan berjalan. Operasi composer dihindari karena platform mismatch beberapa paket dev.)
- Database schema: MySQL 8.4.3, database `silka.` (terhubung via `.env`). Tabel `cluster`, `coa`, `kategori`, `transaksi`, `target_capaians`, `users` dibuat di luar Laravel migration (migration files tidak ada di repo).
- Test awal: hanya `tests/Unit/ExampleTest.php` dan `tests/Feature/ExampleTest.php` bawaan.

## Temuan penting
- ROLE-01: `users.level` = `enum('admin','bendahara')` → **terpecahkan**. Admin = `admin`, staf = `bendahara`.
- SCHEMA-01: `transaksi.coa_id` **tidak ada** → ditambahkan (nullable) + index + FK RESTRICT.
- SCHEMA-02: `coa.saldo` **tidak ada** → ditambahkan `DECIMAL(18,2)` default 0.
- SCHEMA-03: `transaksi.nominal` = `bigint` → diubah ke `DECIMAL(18,2)`.
- SCHEMA-04: `transaksi.kategori_id` = `int` (PK kategori `bigint unsigned`) → disamakan `bigint unsigned` + FK RESTRICT.
- SCHEMA-05: `transaksi.jenis` = `enum('Pemasukan','Pengeluaran')` → dinormalisasi ke canonical `enum('pemasukan','pengeluaran')` (backfill eksplisit, mapping 1:1 dari data legacy).
- SCHEMA-06: `target_capaians.target_capaian` = `bigint` → diubah ke `DECIMAL(18,2)`; unique index `tahun` aman (2022,2023,2024, tidak duplikat).
- ORPHAN-KAT-01: seluruh 1286 transaksi menunjuk `kategori_id` yang sudah tidak ada (master kategori hilang; sisa 1 record id=2). Diresolusi dengan kategori default id=1 `Tanpa Kategori` (CAT-002/003) dan reassign orphan ke id=1, terdokumentasi sebagai deployment step eksplisit.
- COA-CLASS-01: klasifikasi piutang/hutang diambil dari master COA existing: akun bernama mengandung `Piutang` (jenis `Aset`) = piutang; mengandung `Utang` (jenis `Liabilitas`) = hutang. Diterapkan pada dashboard.
- CLUSTER-01: tabel `cluster` kosong, `coa.cluster` seluruhnya NULL. Tidak ada formula cluster existing → cluster dipertahankan opsional/nullable, tidak dibuatkan formula baru.
- DATABASE-01: nama DB aktual `silka.` (dengan titik) sesuai `.env`. Tidak diubah.
- SCHEMA-07 (BARU): tabel `target_capaians` tidak dibuat oleh migration mana pun (hanya dinormalisasi jika sudah ada). Untuk instalasi fresh, ditambahkan migration `2026_08_18_000011_create_target_capaians_table` (idempotent, skip bila tabel sudah ada).

## Progress
- [x] Phase 0 - Audit dan baseline
- [x] Phase 1 - Fondasi, auth, dan schema compatibility
- [x] Phase 2 - Master kategori dan COA
- [x] Phase 3 - Mesin transaksi dan saldo
- [x] Phase 4 - Dashboard dan target capaian
- [x] Phase 5 - Laporan, print, dan Excel
- [x] Phase 6 - Manajemen user dan foto
- [x] Phase 7 - Hardening dan handoff

## Perubahan
### Phase 1 (fondasi, auth, schema)
- File: `routes/web.php`, `app/Http/Controllers/AuthController.php`, `app/Http/Requests/LoginRequest.php`, `app/Http/Middleware/Authenticate.php`, `app/Http/Middleware/RedirectIfAuthenticated.php`, `app/Providers/AuthServiceProvider.php`, `app/Providers/RouteServiceProvider.php`, `app/Models/User.php`, `resources/views/auth/login.blade.php`, `resources/views/layouts/app.blade.php`.
- Migration: `000001` (level+foto), `000002` (cluster), `000003` (coa), `000004` (kategori), `000005` (transaksi + kompatibilitas), `000006` (seed kategori default + reassign orphan), `000008` (PK cluster), `000009` (FK finansial RESTRICT), `000011` (create target_capaians untuk instalasi fresh).
- Keputusan: seluruh route internal ber-middleware `auth`; modul user dilindungi `can:manage-users` (Gate admin) di samping middleware; login memakai throttle `5,1`; `RouteServiceProvider::HOME` diarahkan ke `/dashboard` agar user yang sudah login diarahkan ke dashboard (AUTH-002).
- Asumsi: `users.level` = `admin`/`bendahara` (terverifikasi dari schema enum).

### Phase 2 (kategori & COA)
- File: `app/Http/Controllers/KategoriController.php`, `app/Http/Controllers/CoaController.php`, `app/Http/Requests/{StoreKategoriRequest,UpdateKategoriRequest,StoreCoaRequest,UpdateCoaRequest}.php`, `app/Models/{Kategori,Coa,Cluster}.php`, views `kategori/*`, `coa/*`.
- Keputusan: kategori default id=1 tidak bisa dihapus; hapus kategori memindahkan transaksi ke id=1 dalam satu DB transaction; COA yang dipakai transaksi atau bersaldo non-nol tidak bisa dihapus (COA-DELETE-01); edit COA hanya kode/nama/jenis/cluster (saldo tidak bisa diedit langsung).

### Phase 3 (transaksi & saldo)
- File: `app/Services/TransaksiSaldoService.php`, `app/Http/Controllers/TransaksiController.php`, `app/Http/Requests/{StoreTransaksiRequest,UpdateTransaksiRequest}.php`, `app/Models/Transaksi.php`, views `transaksi/*`.
- Keputusan: create/edit/delete memakai `DB::transaction()` + `lockForUpdate()`; saat edit dua COA terkunci dalam urutan ID ascending; nominal `DECIMAL(18,2)`; pagination 20; filter tanggal/jenis/kategori/COA/keterangan.
- Asumsi: TRX-NEG-01 saldo negatif diizinkan (tidak ada aturan insufficient balance di sumber requirement).

### Phase 4 (dashboard & target)
- File: `app/Http/Controllers/{DashboardController,TargetCapaianController}.php`, `app/Models/TargetCapaian.php`, `app/Http/Requests/{StoreTargetCapaianRequest,UpdateTargetCapaianRequest}.php`, views `dashboard/*`, `target-capaians/*`.
- Migration: `000007` (normalisasi `target_capaian` ke DECIMAL), `000010` (unique index `tahun`).
- Keputusan: dashboard memakai parameter `year` dengan fallback tahun berjalan; kartu piutang/hutang tahun lalu memakai mapping COA-CLASS-01.

### Phase 5 (laporan, print, Excel)
- File: `app/Services/ReportService.php`, `app/Exports/TransaksiReportExport.php`, `app/Http/Controllers/LaporanController.php`, `app/Http/Requests/ReportFilterRequest.php`, views `laporan/*`.
- Keputusan: satu sumber dataset (`ReportService`) untuk web/print/export; export `.xlsx` asli berbasis OOXML/ZipArchive tanpa dependency eksternal; sanitasi formula injection (nilai berawalan `= + - @` diberi apostrof).

### Phase 6 (user & foto)
- File: `app/Http/Controllers/UserController.php`, `app/Http/Requests/{StoreUserRequest,UpdateUserRequest}.php`, views `users/*`.
- Keputusan: upload foto maks 2MB via `Storage::disk('public')` dengan nama file acak; ganti foto membersihkan foto lama setelah update sukses; hapus user membersihkan file; user tidak dapat menghapus diri sendiri; administrator terakhir tidak dapat dihapus.

### Phase 7 (hardening & handoff)
- File: `phpunit.xml` (DB test terpisah `silka_test`), `tests/Feature/AuthTest.php`, `tests/Feature/TransaksiSaldoTest.php`, `tests/Feature/KategoriCoaTest.php`, `tests/Feature/DashboardTargetTest.php`, `tests/Feature/LaporanTest.php`, `tests/Feature/UserTest.php`; `tests/Feature/ExampleTest.php` dan `tests/Unit/ExampleTest.php` dihapus (test bawaan usang).
- Perbaikan: `RouteServiceProvider::HOME` → `/dashboard`; migration baru `000011`; `UserController` memakai disk `public` secara eksplisit untuk konsistensi store/delete.
- Keputusan: seluruh test memakai `RefreshDatabase` pada DB `silka_test` (bukan DB development `silka.`).

## Verifikasi
- Command: `php artisan migrate:status`, `php artisan route:list`, `php artisan migrate --force` (aplikasi migration `000011` pada DB asli, idempotent), `vendor\bin\phpunit`.
- Hasil: seluruh migration ter-record; route internal + throttle terdaftar; migration baru berjalan tanpa merusak data; **61 tests, 245 assertions, OK**.
- Audit tambahan: tidak ditemukan `dd`/`dump`/`var_dump`/`print_r` di `app`, `routes`, `database`; list utama (transaksi, COA, laporan) sudah eager-load relasi untuk mencegah N+1.

## Blocker tersisa
- CLUSTER-01 (parsial, bukan blocker blokir): tabel `cluster` kosong dan `coa.cluster` NULL pada data existing sehingga filter cluster menampilkan daftar kosong sampai master cluster diisi. Bukan blocker karena field opsional dan tidak menampilkan angka tebakan.
- Tidak ada blocker lain. Klasifikasi piutang/hutang memakai mapping existing (COA-CLASS-01) dan tidak menampilkan angka jika tidak ada akun terkait.