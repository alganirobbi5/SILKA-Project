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

## Progress
- [x] Phase 0
- [ ] Phase 1
- [ ] Phase 2
- [ ] Phase 3
- [ ] Phase 4
- [ ] Phase 5
- [ ] Phase 6
- [ ] Phase 7

## Perubahan
- File/migration: (diisi per fase)
- Keputusan: (diisi per fase)
- Asumsi: (diisi per fase)

## Verifikasi
- Command:
- Hasil:

## Blocker tersisa
- Tidak ada / jelaskan secara spesifik.
