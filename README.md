<div align="center">

<img width="100%" src="https://capsule-render.vercel.app/api?type=waving&color=0:020617,50:0F766E,100:22C55E&height=240&section=header&text=SILKA%20FINANCE&fontSize=52&fontColor=FFFFFF&animation=fadeIn&fontAlignY=36&desc=Standalone%20Financial%20%26%20Back%20Office%20System&descAlignY=58&descSize=18" alt="SILKA Finance banner" />

<br />

<img src="https://readme-typing-svg.demolab.com?font=Fira+Code&weight=600&size=18&pause=1000&color=22C55E&center=true&vCenter=true&width=760&lines=Financial+Operations+with+Transactional+Integrity;Built+for+Accuracy%2C+Control%2C+and+Traceability;Laravel+Back+Office+%7C+COA+%7C+Reporting+%7C+User+Management" alt="SILKA typing introduction" />

<br /><br />

<a href="https://laravel.com">
  <img src="https://img.shields.io/badge/Laravel-8.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 8" />
</a>
<a href="https://www.php.net">
  <img src="https://img.shields.io/badge/PHP-7.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 7.4" />
</a>
<a href="https://www.mysql.com">
  <img src="https://img.shields.io/badge/MySQL%20%2F%20MariaDB-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL or MariaDB" />
</a>
<img src="https://img.shields.io/badge/Architecture-Standalone-0F766E?style=for-the-badge&logo=buffer&logoColor=white" alt="Standalone architecture" />

<br />

<img src="https://img.shields.io/badge/Interface-Blade-FF6B6B?style=flat-square&logo=laravel&logoColor=white" alt="Blade interface" />
<img src="https://img.shields.io/badge/Auth-Session%20Based-2563EB?style=flat-square&logo=auth0&logoColor=white" alt="Session based authentication" />
<img src="https://img.shields.io/badge/Export-Excel-217346?style=flat-square&logo=microsoftexcel&logoColor=white" alt="Excel export" />
<img src="https://img.shields.io/badge/Language-Bahasa%20Indonesia-EF4444?style=flat-square" alt="Bahasa Indonesia" />
<img src="https://img.shields.io/badge/Focus-Financial%20Integrity-F59E0B?style=flat-square&logo=shield&logoColor=white" alt="Financial integrity" />

<br /><br />

<strong>Sistem keuangan internal untuk mengelola transaksi, saldo COA, kategori, laporan, target tahunan, dan user dalam satu back office yang terstruktur.</strong>

<br /><br />

<a href="#-tentang-silka">Tentang</a>
&nbsp;&bull;&nbsp;
<a href="#-fitur-utama">Fitur</a>
&nbsp;&bull;&nbsp;
<a href="#-alur-sistem">Alur</a>
&nbsp;&bull;&nbsp;
<a href="#-arsitektur">Arsitektur</a>
&nbsp;&bull;&nbsp;
<a href="#-keamanan--reliability">Keamanan</a>
&nbsp;&bull;&nbsp;
<a href="#-dokumentasi-proyek">Dokumentasi</a>

</div>

---

## 🧭 Tentang SILKA

**SILKA Finance / Back Office** adalah aplikasi web internal yang berdiri secara mandiri untuk membantu pengelolaan aktivitas keuangan operasional. Project ini memusatkan transaksi pemasukan dan pengeluaran, pengelolaan **Chart of Accounts (COA)**, kategori transaksi, laporan, target capaian tahunan, serta akun pengguna.

Aplikasi memiliki autentikasi, konfigurasi, database, dan deployment sendiri. SILKA tidak dibangun sebagai modul internal dari Core System sehingga pengembangannya dapat dilakukan secara terisolasi tanpa mengganggu sistem utama.

> **Engineering principle**  
> Tampilan dapat berubah, tetapi setiap perubahan saldo harus selalu dapat dijelaskan, dihitung ulang, dan dipertanggungjawabkan.

### Repository at a glance

| | Informasi | Detail |
|:--:|---|---|
| 🎯 | Fokus utama | Pengelolaan keuangan dan operasional back office |
| 🧱 | Arsitektur | Standalone web application |
| 🗄️ | Database | MySQL/MariaDB dengan database `silka` |
| 🔐 | Akses | User internal dengan session authentication |
| 💵 | Domain | Pemasukan, pengeluaran, COA, saldo, dan laporan |
| 📊 | Output | Dashboard, web report, print view, dan Excel |
| 🧪 | Prioritas teknis | Konsistensi data, atomic transaction, dan testability |
| 📘 | Acuan implementasi | [`PRD-SILKA.md`](./PRD-SILKA.md) |

---

## ✨ Fitur Utama

<table>
<tr>
<td width="50%" valign="top">
<h3>📊 Financial Dashboard</h3>
<ul>
<li>Ringkasan keuangan berdasarkan tahun.</li>
<li>Total pemasukan dan pengeluaran harian.</li>
<li>Agregasi bulanan dan tahunan.</li>
<li>Target capaian untuk tahun terpilih.</li>
<li>Ringkasan piutang dan hutang berdasarkan klasifikasi COA.</li>
</ul>
</td>
<td width="50%" valign="top">
<h3>💸 Transaction Engine</h3>
<ul>
<li>Pencatatan pemasukan dan pengeluaran.</li>
<li>Pagination 20 data per halaman.</li>
<li>Filter tanggal dan pencarian keterangan.</li>
<li>Penyesuaian saldo COA secara otomatis.</li>
<li>Revert saldo saat transaksi dihapus.</li>
</ul>
</td>
</tr>
<tr>
<td width="50%" valign="top">
<h3>🧾 Chart of Accounts</h3>
<ul>
<li>Pengelolaan master akun COA.</li>
<li>Kode, nama, jenis, cluster, dan saldo akun.</li>
<li>Saldo awal saat akun dibuat.</li>
<li>Filter cluster dan pencarian akun.</li>
<li>Proteksi terhadap penghapusan akun aktif.</li>
</ul>
</td>
<td width="50%" valign="top">
<h3>🏷️ Transaction Categories</h3>
<ul>
<li>Pengelolaan kategori transaksi.</li>
<li>Kategori default sebagai fallback data.</li>
<li>Safety reassignment ketika kategori dihapus.</li>
<li>Perlindungan terhadap orphan transaction.</li>
</ul>
</td>
</tr>
<tr>
<td width="50%" valign="top">
<h3>📑 Financial Reports</h3>
<ul>
<li>Filter berdasarkan kategori dan periode.</li>
<li>Tampilan laporan langsung di web.</li>
<li>Layout khusus untuk kebutuhan print.</li>
<li>Export laporan ke file <code>.xlsx</code>.</li>
<li>Ringkasan pemasukan, pengeluaran, dan nilai bersih.</li>
</ul>
</td>
<td width="50%" valign="top">
<h3>🎯 Annual Targets</h3>
<ul>
<li>Pengelolaan target capaian tahunan.</li>
<li>Satu target unik untuk setiap tahun.</li>
<li>Integrasi langsung dengan dashboard.</li>
<li>Validasi nominal dan tahun target.</li>
</ul>
</td>
</tr>
<tr>
<td width="50%" valign="top">
<h3>👥 User Management</h3>
<ul>
<li>Daftar, tambah, edit, dan hapus user.</li>
<li>Password disimpan menggunakan hash Laravel.</li>
<li>Foto profil maksimal 2 MB.</li>
<li>Penggantian dan pembersihan file foto lama.</li>
<li>Authorization berdasarkan level user existing.</li>
</ul>
</td>
<td width="50%" valign="top">
<h3>🔐 Authentication Layer</h3>
<ul>
<li>Login dan logout berbasis session.</li>
<li>Route internal dilindungi middleware.</li>
<li>Regenerasi session setelah login.</li>
<li>CSRF protection pada setiap mutasi data.</li>
<li>Rate limiting pada percobaan login.</li>
</ul>
</td>
</tr>
</table>

---

## 🧩 Modul Sistem

| Modul | Tanggung jawab | Hasil utama |
|---|---|---|
| **Authentication** | Memvalidasi identitas dan session user | Akses internal yang terlindungi |
| **Dashboard** | Mengagregasi informasi keuangan berdasarkan periode | Ringkasan kondisi keuangan |
| **Transaksi** | Mencatat pemasukan/pengeluaran dan mengubah saldo | Riwayat transaksi yang konsisten |
| **Kategori** | Mengelompokkan transaksi dan menjaga fallback kategori | Data transaksi tetap valid |
| **COA** | Mengelola akun, cluster, jenis, dan saldo | Struktur akun keuangan |
| **Laporan** | Menyaring serta menyajikan data lintas format | Web report, print, dan Excel |
| **Target Capaian** | Menyimpan nominal target setiap tahun | Target tahunan pada dashboard |
| **User** | Mengelola akun, level, password, dan foto | Kontrol pengguna aplikasi |

---

## 🔄 Alur Sistem

```mermaid
flowchart TD
    Start(["User membuka SILKA"]) --> Auth{"Sudah terautentikasi?"}
    Auth -- Tidak --> Login["Halaman login"]
    Login -- Berhasil --> Dashboard["Dashboard keuangan"]
    Auth -- Ya --> Dashboard
    Dashboard --> Menu{"Pilih modul"}
    Menu --> Transaction["Transaksi & saldo COA"]
    Menu --> Master["Kategori & master COA"]
    Menu --> Report["Laporan & target tahunan"]
    Menu --> User["Manajemen user"]
```

<details>
<summary><strong>⚙️ Lihat siklus transaksi dan saldo COA</strong></summary>

```mermaid
flowchart TD
    Input["Input transaksi"] --> Validation{"Data valid?"}
    Validation -- Tidak --> Reject["Tolak dan tampilkan error"]
    Validation -- Ya --> Lock["Lock COA terkait"]
    Lock --> Type{"Jenis transaksi"}
    Type -- Pemasukan --> Add["Saldo + nominal"]
    Type -- Pengeluaran --> Subtract["Saldo - nominal"]
    Add --> Commit["Commit transaksi dan saldo"]
    Subtract --> Commit
```

</details>

---

## 🧮 Financial Integrity

Inti dari SILKA berada pada konsistensi antara transaksi dan saldo akun. Seluruh operasi finansial harus dijalankan secara atomik menggunakan database transaction dan row locking.

```text
Pemasukan   : saldo_baru = saldo_lama + nominal
Pengeluaran : saldo_baru = saldo_lama - nominal

Edit        : saldo_baru = saldo_sekarang
                           - efek_transaksi_lama
                           + efek_transaksi_baru

Delete      : saldo_baru = saldo_sekarang
                           - efek_transaksi_yang_dihapus
```

### Transaction safety rules

- **Create** menyimpan transaksi dan memperbarui saldo dalam satu commit.
- **Edit** merevert efek lama sebelum menerapkan efek transaksi baru.
- **Delete** mengembalikan pengaruh saldo sebelum menghapus record.
- **Rollback** dijalankan jika salah satu proses gagal.
- **Row lock** melindungi saldo dari perubahan request yang berjalan bersamaan.
- **Decimal value** digunakan untuk menghindari kehilangan presisi nilai uang.

> **Correctness first.** Tidak boleh ada transaksi tanpa akun COA yang valid dan tidak boleh ada perubahan saldo setengah tersimpan.

---

## 🗃️ Data Domain

```mermaid
erDiagram
    CLUSTER ||--o{ COA : mengelompokkan
    COA ||--o{ TRANSAKSI : digunakan_oleh
    KATEGORI ||--o{ TRANSAKSI : mengklasifikasikan

    CLUSTER {
        int id_cluster PK
        string nama
    }
    COA {
        int id PK
        string kode_coa
        string nama_coa
        string jenis
        decimal saldo
    }
    KATEGORI {
        int id PK
        string kategori
    }
    TRANSAKSI {
        int id PK
        date tanggal
        string jenis
        decimal nominal
        int kategori_id FK
        int coa_id FK
    }
    TARGET_CAPAIANS {
        int id PK
        int tahun
        decimal target_capaian
    }
```

> Diagram menampilkan model domain yang dibutuhkan aplikasi. Detail tipe primary key, nama foreign key, dan kompatibilitas schema mengikuti database existing serta migration yang sudah diverifikasi.

---

## 🧱 Arsitektur

```mermaid
flowchart TD
    View["Blade Views"] --> Controller["HTTP Controllers"]
    Controller --> Request["Form Requests & Authorization"]
    Controller --> Service["Transaction & Report Services"]
    Service --> Model["Eloquent Models"]
    Model --> Database[("MySQL / MariaDB")]
```

### Separation of concerns

| Layer | Peran |
|---|---|
| **Views** | Menampilkan data, state, validasi, dan interaksi antarmuka |
| **Controllers** | Mengatur request/response tanpa menampung rumus saldo kompleks |
| **Form Requests** | Menangani validasi dan authorization request |
| **Services** | Menjadi pusat aturan transaksi, saldo, dan laporan |
| **Models** | Mendefinisikan relasi, casts, dan akses data |
| **Database** | Menjaga constraint, index, foreign key, dan atomic transaction |

### Technology stack

| Area | Teknologi |
|---|---|
| Backend framework | Laravel 8.x |
| Runtime | PHP 7.4.x |
| Database | MySQL/MariaDB |
| ORM | Laravel Eloquent |
| Interface | Blade server-rendered views |
| Authentication | Laravel session authentication |
| Validation | Form Request / Laravel Validator |
| File storage | Laravel Filesystem |
| Reporting | Web view, print view, dan Excel `.xlsx` |
| Testing | Laravel/PHP automated tests |

---

## 🛡️ Keamanan & Reliability

<div align="center">

<img src="https://img.shields.io/badge/CSRF-Protected-16A34A?style=flat-square&logo=shield&logoColor=white" alt="CSRF protected" />
<img src="https://img.shields.io/badge/Passwords-Hashed-7C3AED?style=flat-square&logo=keepassxc&logoColor=white" alt="Hashed passwords" />
<img src="https://img.shields.io/badge/Database-Atomic-0284C7?style=flat-square&logo=databricks&logoColor=white" alt="Atomic database" />
<img src="https://img.shields.io/badge/Uploads-Validated-EA580C?style=flat-square&logo=files&logoColor=white" alt="Validated uploads" />
<img src="https://img.shields.io/badge/Queries-Bound-0F766E?style=flat-square&logo=mysql&logoColor=white" alt="Bound database queries" />

</div>

- Seluruh route internal dilindungi authentication middleware.
- Authorization dilakukan di backend, bukan hanya dengan menyembunyikan menu.
- Password tidak pernah disimpan atau dibandingkan dalam bentuk plain text.
- Seluruh request mutasi dilindungi CSRF.
- Login menggunakan session regeneration dan rate limiting.
- File foto divalidasi berdasarkan ukuran dan tipe file sebenarnya.
- Nama file upload dibuat ulang untuk menghindari path dan filename abuse.
- Query menggunakan Eloquent/query binding untuk menghindari SQL injection.
- Export Excel melindungi input user dari formula injection.
- Migration tidak boleh menjalankan penghapusan data secara otomatis.
- Error production tidak membocorkan stack trace, token, atau kredensial.

---

## 🧪 Quality Standard

Project ini dirancang dengan pengujian yang berfokus pada aturan bisnis, bukan hanya keberhasilan halaman dimuat.

```text
AUTHENTICATION  → guest protection, login, logout, authorization
TRANSACTION     → create, edit, delete, rollback, saldo adjustment
MASTER DATA     → category fallback, COA constraints, unique values
DASHBOARD       → period selection, aggregation, empty state
REPORTING       → filter consistency, totals, print, valid Excel output
USER            → password hashing, upload validation, access control
```

### Definition of quality

- Perhitungan saldo memiliki automated tests.
- Web, print, dan Excel menggunakan sumber query yang konsisten.
- Tidak ada orphan category atau COA reference.
- Tidak ada secret, debug dump, atau konfigurasi sensitif di repository.
- List utama menggunakan pagination dan menghindari N+1 query.
- Interface tetap terbaca pada desktop, tablet, dan mobile.
- Blocker schema atau aturan bisnis tidak diselesaikan dengan data tebakan.

---

## 🗺️ Development Blueprint

```mermaid
flowchart TD
    Audit["01 - Audit repository & schema"] --> Foundation["02 - Auth & database foundation"]
    Foundation --> Master["03 - Category & COA master"]
    Master --> Transaction["04 - Transaction & balance engine"]
    Transaction --> Dashboard["05 - Dashboard & annual targets"]
    Dashboard --> Report["06 - Reports, print & Excel"]
    Report --> Users["07 - User management"]
    Users --> Hardening["08 - Tests, security & handoff"]
```

Setiap tahap memiliki acceptance criteria dan quality gate sendiri. Detail eksekusi, asumsi, schema gap, serta test plan tersedia pada dokumen PRD.

---

## 📘 Dokumentasi Proyek

| Dokumen | Keterangan |
|---|---|
| [`README.md`](./README.md) | Gambaran umum dan identitas repository |
| [`PRD-SILKA.md`](./PRD-SILKA.md) | Product requirement, aturan bisnis, arsitektur, fase implementasi, dan acceptance criteria |
| `IMPLEMENTATION_LOG.md` | Catatan audit, progress fase, keputusan teknis, hasil test, dan blocker implementasi |

### Catatan implementasi penting

Beberapa bagian wajib diverifikasi dari source code atau schema existing sebelum implementasi:

- keberadaan relasi `transaksi.coa_id`;
- keberadaan kolom saldo pada tabel `coa`;
- arti nilai yang digunakan pada `users.level`;
- klasifikasi COA untuk piutang dan hutang;
- pola cluster yang digunakan ketika COA dibuat.

Nilai tersebut tidak boleh ditebak karena berpengaruh langsung pada authorization dan akurasi data finansial.

---

## 💡 Project Philosophy

<div align="center">

### `Readable code. Predictable flow. Explainable balance.`

SILKA dibangun dengan prinsip bahwa software keuangan harus mudah dipahami oleh user, dapat dipelihara oleh developer, dan tetap konsisten saat menghadapi kegagalan maupun request yang berjalan bersamaan.

<br />

<img src="https://img.shields.io/badge/Designed%20for-Accuracy-22C55E?style=for-the-badge&logo=target&logoColor=white" alt="Designed for accuracy" />
<img src="https://img.shields.io/badge/Engineered%20with-Integrity-0EA5E9?style=for-the-badge&logo=codefactor&logoColor=white" alt="Engineered with integrity" />
<img src="https://img.shields.io/badge/Powered%20by-Clean%20Logic-8B5CF6?style=for-the-badge&logo=codacy&logoColor=white" alt="Powered by clean logic" />

</div>

<br />

<div align="center">

<sub>Financial Operations • Transactional Integrity • Clean Architecture</sub>

<br /><br />

<img width="100%" src="https://capsule-render.vercel.app/api?type=waving&color=0:22C55E,50:0F766E,100:020617&height=130&section=footer" alt="SILKA footer" />

</div>
