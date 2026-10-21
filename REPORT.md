# REPORT.md — Hamta Healthy Food (Hamtash Salad)

**Tanggal laporan:** 21 Oktober 2026
**Branch aktif:** `feat/admin` (commit terakhir: `f74b035 feat: buat seeder`)
**Dasar laporan:** seluruh file di repo ini dibaca langsung (migration, model, seeder, controller, view, route, config, env, dan dokumen acuan). Data runtime diverifikasi dengan query ke database `hamta_db` dan simulasi request HTTP ke kernel Laravel.

> Catatan untuk AI assistant sesi berikutnya: dokumen ini desksriptif, bukan aspirasional. Jika sesuatu tertulis "BELUM ADA" di sini, berarti benar-benar belum ada filenya di repo — jangan diasumsikan sudah ada.

---

## 1. Ringkasan Umum

### 1.1 Stack PHP (dari `composer.json` + `vendor/`)

| Komponen | Constraint di composer.json | Status |
|---|---|---|
| PHP | `^8.3` | Runtime terpasang **PHP 8.5.8** (ZTS, Windows) |
| laravel/framework | `^13.17` | Terpasang |
| laravel/tinker | `^3.0` | Terpasang |
| mallardduck/blade-lucide-icons | `^2.0` | **Terpasang** (`vendor/mallardduck/blade-lucide-icons`) → dipakai sebagai `<x-lucide-*>` di semua blade admin |
| fakerphp/faker | `^1.23` (dev) | Terpasang |
| laravel/boost | `^2.10` (dev) | Terpasang (menyediakan route `_boost/browser-logs`) |
| laravel/pail | `^1.2.5` (dev) | Terpasang |
| laravel/pao | `^1.0.6` (dev) | Terpasang |
| laravel/pint | `^1.27` (dev) | Terpasang |
| mockery/mockery | `^1.6` (dev) | Terpasang |
| nunomaduro/collision | `^8.6` (dev) | Terpasang |
| phpunit/phpunit | `^12.5.12` (dev) | Terpasang |

**Tidak ada** Breeze/Jetstream/Sanctum/Socialite — sesuai keputusan proyek (auth admin manual).

### 1.2 Stack frontend (dari `package.json` + `node_modules/`)

| Komponen | Constraint | Terpasang |
|---|---|---|
| tailwindcss | `^4.3.3` | 4.3.3 |
| @tailwindcss/vite | `^4.3.3` | 4.3.3 |
| laravel-vite-plugin | `^3.1` | 3.2.0 |
| vite | `^8.0.0` | 8.3.4 |
| concurrently | `^10.0.3` | 10.0.6 |
| @laravel/multiplex | `^0.4.1` (optional) | **UNMET** (opsional, tidak dipakai) |

Node runtime: **v22.21.1**, npm **11.19.0**.

### 1.3 Status environment (Tailwind / Lucide / Chart.js)

- **Tailwind CSS v4 — OK, terkonfigurasi.**
  - `vite.config.js`: plugin `laravel()` (input `resources/css/app.css` + `resources/js/app.js`, `refresh: true`, fonts `bunny('Instrument Sans', weights 400/500/600)`), plugin `tailwindcss()`.
  - `resources/css/app.css` (9 baris): `@import 'tailwindcss';`, dua baris `@source` (pagination Laravel + storage/framework/views), dan blok `@theme` yang hanya mendefinisikan `--font-sans: 'Instrument Sans'`.
  - **Catatan:** palet warna Hamta (`#65BD5E`, `#0B412B`, `#F7F2E7`, `#010101`, `#8A8A8A`, `#E05C5C`) **belum** didefinisikan sebagai token `@theme` — semua warna masih ditulis sebagai arbitrary value hex di tiap blade.
- **Lucide — OK (via PHP, bukan npm).** Dipakai di 7 blade admin, contoh: `<x-lucide-home>`, `<x-lucide-calculator>`, `<x-lucide-concierge-bell>`, `<x-lucide-clipboard-list>`, `<x-lucide-tag>`, `<x-lucide-store>`, `<x-lucide-shopping-bag>`, `<x-lucide-chart-column-increasing>`, `<x-lucide-clock>`, `<x-lucide-check>`, `<x-lucide-search>`, `<x-lucide-image>`, `<x-lucide-pencil>`, `<x-lucide-trash-2>`, `<x-lucide-eye>`, `<x-lucide-filter>`, `<x-lucide-shopping-cart>`. Tidak ada paket npm lucide.
- **Chart.js — BELUM ADA.** Tidak ada di `package.json`, tidak ada di `node_modules`, tidak ada di `resources/js/app.js`. Grafik di `admin/dashboard.blade.php` adalah **SVG statis buatan tangan** (`<polyline>` + `<circle>`), bukan Chart.js. Klaim "Chart.js sudah dikerjakan terpisah" **tidak terbukti ada di repo ini**.
- **`resources/js/app.js` kosong** (1 baris: `//`). Belum ada Alpine.js, axios, atau library JS lain.
- **Build asset:** `public/build/` **tidak ada** (belum pernah `npm run build`). Yang aktif adalah Vite dev server: `public/hot` berisi `http://[::1]:5173`, dan server tersebut hidup (dicek `GET /@vite/client` → HTTP 200, `GET /resources/css/app.css` → HTTP 200).
  - Risiko: kalau dev server dimatikan, `public/hot` masih ada → Blade tetap render 200 tapi aset CSS/JS gagal dimuat di browser.
- **Font duplikat (perlu diputuskan):** `app.css`/`vite.config.js` memakai **Instrument Sans** (bunny fonts), sedangkan semua blade admin memuat **Plus Jakarta Sans** dari Google Fonts lewat `<link>` inline + `body { font-family: 'Plus Jakarta Sans' }`. `DESIGN.md` hanya menyebut "font sans-serif geometric/rounded" tanpa nama pasti.
- **`.env` (local):** `APP_NAME=Laravel` (belum diubah ke "Hamta"), `APP_URL=http://localhost:8000`, `APP_DEBUG=true`, `DB_CONNECTION=mysql` / `hamta_db` @ `127.0.0.1:3306` user `root`, `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database`, `FILESYSTEM_DISK=local`. **Tidak ada** key reCAPTCHA (`RECAPTCHA_*`) dan tidak ada `config/recaptcha.php` —although `config/` hanya berisi file default Laravel (app, auth, cache, database, filesystems, logging, mail, queue, services, session).
- **Git:** branch `feat/admin` (remote `origin/feat/admin` ada), `main` juga ada. Working tree saat laporan dibuat **untracked**: `app/Http/Controllers/Admin/` (7 file), `app/Http/Controllers/KomponenSaladController.php`, `app/Http/Controllers/PesananController.php`, `app/Http/Controllers/ProdukController.php`, `app/Http/Controllers/RatingController.php` — controller customer + admin yang dibuat sesi ini **belum di-commit**.

---

## 2. Database & Migration

Database aktif: MySQL, database `hamta_db`. Semua 16 migration **berstatus Ran** (batch 1) — `php artisan migrate:status` mengonfirmasi.

### 2.1 Daftar migration (urut timestamp filename)

| # | File migration | Tabel |
|---|---|---|
| 1 | `0001_01_01_000000_create_users_table.php` | `users`, `password_reset_tokens`, `sessions` (default Laravel) |
| 2 | `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` (default Laravel) |
| 3 | `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs` (default Laravel) |
| 4 | `2026_10_21_020130_create_cabang_table.php` | `cabang` |
| 5 | `2026_10_21_020133_create_admin_table.php` | `admin` |
| 6 | `2026_10_21_020135_create_bahan_table.php` | `bahan` |
| 7 | `2026_10_21_020138_create_produk_table.php` | `produk` |
| 8 | `2026_10_21_020140_create_produk_bahan_table.php` | `produk_bahan` |
| 9 | `2026_10_21_020143_create_komponen_salad_table.php` | `komponen_salad` |
| 10 | `2026_10_21_020145_create_komponen_bahan_table.php` | `komponen_bahan` |
| 11 | `2026_10_21_020147_create_promo_table.php` | `promo` |
| 12 | `2026_10_21_020149_create_pesanan_table.php` | `pesanan` |
| 13 | `2026_10_21_020151_create_detail_pesanan_table.php` | `detail_pesanan` |
| 14 | `2026_10_21_020154_create_detail_pesanan_komponen_table.php` | `detail_pesanan_komponen` |
| 15 | `2026_10_21_020156_create_pesanan_promo_table.php` | `pesanan_promo` |
| 16 | `2026_10_21_020158_create_rating_table.php` | `rating` |

### 2.2 Struktur per tabel (kolom + FK)

**`cabang`** — `id_cabang` INT UNSIGNED AI PK · `nama_cabang` VARCHAR(100) · `alamat` VARCHAR(255) · `jam_buka` TIME · `jam_tutup` TIME · `layani_pickup` BOOL default true · `layani_dinein` BOOL default false · `created_at`/`updated_at` TIMESTAMP. Tanpa FK.

**`admin`** — `id_admin` INT UNSIGNED AI PK · `nama` VARCHAR(100) · `username` VARCHAR(50) UNIQUE · `password` VARCHAR(255) · `created_at`/`updated_at`. Tanpa FK.

**`bahan`** — `id_bahan` INT UNSIGNED AI PK · `nama_bahan` VARCHAR(100) · `keterangan` VARCHAR(255) NULL (info alergen/komposisi) · `created_at`/`updated_at`. Tanpa FK.

**`produk`** — `id_produk` INT UNSIGNED AI PK · `nama_produk` VARCHAR(100) · `deskripsi` TEXT NULL · `harga_offline` DECIMAL(10,2) · `harga_online` DECIMAL(10,2) · `foto` VARCHAR(255) NULL · `status_aktif` BOOL default true · `bisa_dikustom` BOOL default false · `id_cabang` INT UNSIGNED NULL **FK→cabang ON DELETE SET NULL** · `created_at`/`updated_at`.

**`produk_bahan`** (junction, tanpa timestamps) — `id_produk` **FK→produk ON DELETE CASCADE** · `id_bahan` **FK→bahan ON DELETE CASCADE** · PK komposit (`id_produk`,`id_bahan`).

**`komponen_salad`** — `id_komponen` INT UNSIGNED AI PK · `nama_komponen` VARCHAR(100) · `jenis` ENUM('sayuran','protein') · `harga_tambahan` DECIMAL(10,2) default 0 · `foto` VARCHAR(255) NULL · `deskripsi` VARCHAR(255) NULL · `status_aktif` BOOL default true · `created_at`/`updated_at`. Tanpa FK.

**`komponen_bahan`** (junction, tanpa timestamps) — `id_komponen` **FK→komponen_salad ON DELETE CASCADE** · `id_bahan` **FK→bahan ON DELETE CASCADE** · PK komposit.

**`promo`** — `id_promo` INT UNSIGNED AI PK · `nama_promo` VARCHAR(100) · `deskripsi` VARCHAR(255) NULL · `tipe_potongan` ENUM('nominal','persen','gratis_ongkir') · `nilai_potongan` DECIMAL(10,2) NULL · `syarat_min_porsi` INT UNSIGNED NULL · `batas_penggunaan` INT UNSIGNED NOT NULL · `jumlah_terpakai` INT UNSIGNED default 0 · `status_aktif` BOOL default true · `tanggal_mulai` DATE NULL · `tanggal_selesai` DATE NULL · `created_at`/`updated_at`. Tanpa FK.

**`pesanan`** — `id_pesanan` INT UNSIGNED AI PK · `kode_pesanan` VARCHAR(20) UNIQUE · `jenis` ENUM('online','kasir') · `nama_pelanggan` VARCHAR(100) NULL · `no_whatsapp` VARCHAR(20) NULL · `metode_ambil` ENUM('pickup','delivery') default 'pickup' · `id_cabang` INT UNSIGNED **FK→cabang (tanpa onDelete)** · `alamat_delivery` VARCHAR(255) NULL · `ongkir` DECIMAL(10,2) NULL · `status_ongkir` ENUM('tidak_perlu','menunggu_konfirmasi','terkonfirmasi') default 'tidak_perlu' · `subtotal` DECIMAL(10,2) · `total_diskon` DECIMAL(10,2) default 0 · `total_bayar` DECIMAL(10,2) · `metode_bayar` ENUM('qris','tunai') default 'qris' · `bukti_bayar` VARCHAR(255) NULL · `status_pembayaran` ENUM('menunggu_verifikasi','terverifikasi','ditolak') default 'menunggu_verifikasi' · `status_pesanan` ENUM('diproses','siap','selesai','batal') default 'diproses' · `id_admin` INT UNSIGNED NULL **FK→admin ON DELETE SET NULL** · `created_at`/`updated_at`.

**`detail_pesanan`** — `id_detail` INT UNSIGNED AI PK · `id_pesanan` **FK→pesanan ON DELETE CASCADE** · `id_produk` INT UNSIGNED NULL **FK→produk ON DELETE SET NULL** · `nama_item` VARCHAR(100) (snapshot) · `jumlah` INT UNSIGNED default 1 · `harga_satuan` DECIMAL(10,2) (snapshot) · `subtotal_item` DECIMAL(10,2) · `status_item` ENUM('aktif','dibatalkan') default 'aktif' · **hanya `created_at`** (tanpa `updated_at`).

**`detail_pesanan_komponen`** (junction, tanpa timestamps) — `id_detail` **FK→detail_pesanan ON DELETE CASCADE** · `id_komponen` **FK→komponen_salad (tanpa onDelete)** · `harga_tambahan` DECIMAL(10,2) default 0 (snapshot) · PK komposit.

**`pesanan_promo`** (junction, tanpa timestamps) — `id_pesanan` **FK→pesanan ON DELETE CASCADE** · `id_promo` **FK→promo (tanpa onDelete)** · `nilai_potongan` DECIMAL(10,2) NOT NULL (snapshot) · PK komposit.

**`rating`** — `id_rating` INT UNSIGNED AI PK · `id_pesanan` INT UNSIGNED **UNIQUE FK→pesanan ON DELETE CASCADE** · `bintang` TINYINT UNSIGNED · `ulasan` TEXT NULL · **hanya `created_at`**.

### 2.3 Perbandingan dengan `schema.sql`

**Tabel di schema.sql yang BELUM punya migration:** tidak ada. Ke-13 tabel di `schema.sql` (cabang, admin, bahan, produk, produk_bahan, komponen_salad, komponen_bahan, promo, pesanan, detail_pesanan, detail_pesanan_komponen, pesanan_promo, rating) semuanya punya migration.

**Tabel hasil migration yang TIDAK ada di schema.sql** (default Laravel, tidak ada di acuan): `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`. `sessions` memang dibutuhkan karena `SESSION_DRIVER=database`. `users` + `User` model adalah sisa skeleton Laravel — **tidak dipakai** karena auth admin pakai tabel `admin` manual.

**Perbedaan struktur migration vs `schema.sql`:**

1. **`rating.bintang` — BEDA.**
   - `schema.sql`: `bintang TINYINT UNSIGNED NOT NULL CHECK (bintang BETWEEN 1 AND 5)`.
   - Migration: `$table->unsignedTinyInteger('bintang');` → **tidak ada CHECK constraint**. Range 1–5 hanya dijaga di level aplikasi (`RatingController` validasi `min:1|max:5`). Tidak boleh diedit file migration yang sudah Ran — perlu migration baru bila mau sinkron.
2. **Selain poin di atas, tidak ada perbedaan kolom/FK/tipe** antara 13 tabel domain dan `schema.sql`. Perbedaan timestamp (`$table->timestamps()` vs `TIMESTAMP DEFAULT CURRENT_TIMESTAMP`) secara semantik setara.

**Catatan FK yang affects implementasi:**
- `pesanan_promo.id_promo` dan `detail_pesanan_komponen.id_komponen` **tanpa `onDelete`** → hapus `promo`/`komponen_salad` yang sudah terpakai akan gagal dengan error FK. `Admin\PromoController::destroy()` sudah pakai guard (cek `$promo->pesanan()->exists()` → nonaktifkan, bukan hapus). Belum ada guard serupa untuk hapus `komponen_salad`.
- `pesanan.id_cabang` juga tanpa `onDelete` → cabang yang sudah dipakai pesanan tidak bisa dihapus.

---

## 3. Model Eloquent

Semua file di `app/Models/` (13 file):

| File | Tabel | Primary key | Relasi |
|---|---|---|---|
| `Admin.php` | `admin` | `id_admin` | `pesanan()` hasMany → `Pesanan` (`id_admin`) |
| `Bahan.php` | `bahan` | `id_bahan` | `produk()` belongsToMany → `Produk` (pivot `produk_bahan`) · `komponenSalad()` belongsToMany → `KomponenSalad` (pivot `komponen_bahan`) |
| `Cabang.php` | `cabang` | `id_cabang` | `produk()` hasMany → `Produk` (`id_cabang`) · `pesanan()` hasMany → `Pesanan` (`id_cabang`) |
| `DetailPesanan.php` | `detail_pesanan` | `id_detail` | `pesanan()` belongsTo → `Pesanan` · `produk()` belongsTo → `Produk` · `komponenSalad()` belongsToMany → `KomponenSalad` (pivot `detail_pesanan_komponen`, `withPivot('harga_tambahan')`) |
| `DetailPesananKomponen.php` | `detail_pesanan_komponen` | — (komposit, `$incrementing=false`, `$timestamps=false`) | `detailPesanan()` belongsTo → `DetailPesanan` · `komponenSalad()` belongsTo → `KomponenSalad` |
| `KomponenBahan.php` | `komponen_bahan` | — (komposit, `$incrementing=false`, `$timestamps=false`) | `komponenSalad()` belongsTo → `KomponenSalad` · `bahan()` belongsTo → `Bahan` |
| `KomponenSalad.php` | `komponen_salad` | `id_komponen` | `bahan()` belongsToMany → `Bahan` (pivot `komponen_bahan`) · `detailPesanan()` belongsToMany → `DetailPesanan` (pivot `detail_pesanan_komponen`, `withPivot('harga_tambahan')`) |
| `Pesanan.php` | `pesanan` | `id_pesanan` | `cabang()` belongsTo → `Cabang` · `admin()` belongsTo → `Admin` · `detailPesanan()` hasMany → `DetailPesanan` · `promo()` belongsToMany → `Promo` (pivot `pesanan_promo`, `withPivot('nilai_potongan')`) · `rating()` hasOne → `Rating` |
| `Promo.php` | `promo` | `id_promo` | `pesanan()` belongsToMany → `Pesanan` (pivot `pesanan_promo`) |
| `Produk.php` | `produk` | `id_produk` | `cabang()` belongsTo → `Cabang` · `bahan()` belongsToMany → `Bahan` (pivot `produk_bahan`) · `detailPesanan()` belongsTo → `DetailPesanan` |
| `ProdukBahan.php` | `produk_bahan` | — (komposit, `$incrementing=false`, `$timestamps=false`) | `produk()` belongsTo → `Produk` · `bahan()` belongsTo → `Bahan` |
| `Rating.php` | `rating` | `id_rating` | `pesanan()` belongsTo → `Pesanan` (`const UPDATED_AT = null`) |
| `User.php` | `users` | `id` (default) | Default Laravel `Authenticatable` + `HasFactory` + `Notifiable` — **tidak dipakai** di alur aplikasi ini |

### 3.1 Relasi yang mengarah ke model belum ada

**Tidak ada.** Semua target relasi (`Produk`, `Bahan`, `KomponenSalad`, `Pesanan`, `DetailPesanan`, `Promo`, `Cabang`, `Admin`, `Rating`) punya file model yang sudah ada. Tidak ada relasi yatim.

### 3.2 Temuan/bug pada model (terverifikasi)

1. **`Cabang::$fillable` salah ketik — BUG AKTIF.**
   - Isi `$fillable`: `nama_cabang, alamat, jam_buka, jam_tutup, layanan_pickup, layanan_dinein`.
   - Nama kolom di DB: **`layani_pickup`, `layani_dinein`** (tanpa huruf "an" di tengah).
   - Dampak 1: `CabangSeeder` mengirim key `layani_pickup`/`layani_dinein` → key tersebut **tidak ada di `$fillable`** → Eloquent membuangnya diam-diam → yang tersimpan adalah nilai default DB (`layani_pickup`=1, `layani_dinein`=0). **Diverifikasi**: insert test dengan `layani_dinein => true` menghasilkan `layani_dinein = 0`.
   - Dampak 2: mem-*create/update* Cabang dengan key `layanan_pickup` (sesuai fillable) akan memicu error SQL `Unknown column 'layanan_pickup'`.
   - Catatan data: baris MERR yang ada di DB saat ini **sudah** punya `layani_dinein = 1` (terinsert sebelum fillable ini diubah / via jalur lain), jadi `php artisan db:seed` ulang akan mengubah nilainya jadi 0.
2. **Tidak ada `casts` di model domain mana pun.** Kolom DECIMAL (`harga_online`, `total_bayar`, `harga_tambahan`) dan BOOLEAN (`status_aktif`) kembali dari DB sebagai string/ int mentah. Contoh terverifikasi: `LaporanController` menghasilkan `total_pendapatan = "45000.00"` (string), dan Blade harus melakukan casting sendiri.
3. **Tidak ada `SoftDeletes` di model mana pun** → `Admin\MenuController::destroy()` benar-benar hard delete.
4. **Tidak ada atribut `status_aktif`/`jumlah_terpakai` yang di-maintenance otomatis** di model `Promo` → auto-nonaktif kuota harus dipanggil manual dari controller.
5. `Admin` tidak mewarisi `Authenticatable` dan tidak ada guard `'admin'` di `config/auth.php` → memang sesuai desain (auth manual), tapi berarti `auth()` helper tidak bisa dipakai, dan `Admin` model **tidak punya** relasi balik ke `pesanan()` via `Pesanan::admin()` — sebenarnya ada, jadi lengkap.

---

## 4. Seeder

Semua file di `database/seeders/` (5 file):

| File | Tabel | Jumlah baris | Status data |
|---|---|---|---|
| `AdminSeeder.php` | `admin` | **1** | Username `admin`, password `Hash::make('hamta')`, nama "Kak Yunita". **Kredensial dummy yang diketahui luas** — wajib diganti sebelum demo/ujian. |
| `CabangSeeder.php` | `cabang` | **2** | Lidah Kulon & MERR. Jam buka/tutup `09:00`–`21:00` keduanya. **Alamat MERR masih placeholder** (komentar di file: "placeholder -- alamat lengkap belum didapat dari wawancara"). **Nilai `layani_pickup`/`layani_dinein` TIDAK tersimpan** karena bug `$fillable` (lihat §3.2) — baris MERR sekarang menyimpan `layani_dinein = 1` dari insert sebelumnya, tapi seed ulang akan mengubahnya ke 0. |
| `BahanSeeder.php` | `bahan` | **11** (7 MQ: Selada, Romaine, Kol Ungu, Tomat, Timun, Kacang Polong, Mixed Vegetables + 4 MP: Chicken Patties, Beef Patties, Grilled Chicken, Telur) | **`keterangan` = NULL semua** (TODO di file: "Isi info alergen per bahan setelah konfirmasi ke owner (Kak Yunita)"). |
| `KomponenSaladSeeder.php` | `komponen_salad` | **11** (7 MQ + 4 MP) | **`harga_tambahan` = 0 untuk semua** (TODO di file: "Konfirmasi harga tambahan per komponen ke owner (Kak Yunita)"). **`foto` = NULL**, **`deskripsi` = NULL** semua. |
| `DatabaseSeeder.php` | — | — | memanggil `AdminSeeder`, `CabangSeeder`, `BahanSeeder`, `KomponenSaladSeeder` → **keempat seeder sudah terdaftar**. |

### 4.1 Seeder yang BELUM ada

- **Tidak ada `ProdukSeeder`** → tabel `produk` **kosong** (0 baris) dan `produk_bahan` kosong (0 baris).
- **Tidak ada `PromoSeeder`** → tabel `promo` kosong (0 baris).
- Tidak ada seeder untuk `pesanan`, `rating`, `pesanan_promo` (wajar, data transaksi).

### 4.2 Status data nyata di DB `hamta_db` (diverifikasi via query)

```
cabang 2 | admin 1 | bahan 11 | komponen_salad 11
produk 0 | produk_bahan 0 | komponen_bahan 0 | promo 0
pesanan 0 | detail_pesanan 0 | detail_pesanan_komponen 0 | pesanan_promo 0 | rating 0 | users 0
```

Konsekuensi: katalog customer kosong, halaman kasir tidak punya produk untuk dipilih, laporan selalu kosong, dan **harga custom salad = 0** (produk dasar + `harga_tambahan` komponen yang masih 0).

---

## 5. Controller

Semua file di `app/Http/Controllers/`:

| File | Method | Ringkasan | View yang dipakai | File view ada? | Route? |
|---|---|---|---|---|---|
| `Controller.php` | — | Abstract base Laravel (kosong) | — | — | — |
| `ProdukController.php` | `index()` | Ambil produk `status_aktif = true` + `with('cabang')` → view katalog | `katalog` | ❌ tidak ada | ❌ tidak ada |
| | `show($id)` | Detail produk `with(['cabang','bahan'])` → view detail | `produk-detail` | ❌ tidak ada | ❌ tidak ada |
| `KomponenSaladController.php` | `index()` | Ambil komponen aktif, pecah jadi `$sayuran` (jenis='sayuran') & `$protein` (jenis='protein') | `kreasikan-salad` | ❌ tidak ada | ❌ tidak ada |
| `PesananController.php` | `store(Request)` | Checkout guest: validasi input, **hitung ulang harga dari DB** (`harga_online` produk + `harga_tambahan` komponen), simpan `pesanan` (jenis='online', `status_pembayaran='menunggu_verifikasi'`, `status_pesanan='diproses'`) + `detail_pesanan` + pivot `detail_pesanan_komponen`, dibungkus `DB::transaction()`. Ongkir delivery = NULL + `status_ongkir='menunggu_konfirmasi'` | redirect ke `pesanan.show` | ❌ view `status-pesanan` tidak ada | ❌ tidak ada |
| | `show($kode_pesanan)` | Cari pesanan via `where('kode_pesanan')->firstOrFail()` + `with(['cabang','detailPesanan.produk','detailPesanan.komponenSalad'])` | `status-pesanan` | ❌ tidak ada | ❌ tidak ada |
| | `generateKodePesanan()` (private) | Kode unik `HMT-ymd-XXXXX`, cek `Pesanan::where('kode_pesanan',...)->exists()` | — | — | — |
| `RatingController.php` | `store(Request, $kode_pesanan)` | Validasi `bintang` 1–5 + `ulasan` opsional; cari pesanan by kode; cek `Rating::where('id_pesanan',...)->exists()` → kalau sudah ada, redirect dengan pesan error (bukan error DB mentah); kalau belum, `Rating::create()` | redirect ke `pesanan.show` | ❌ tidak ada | ❌ tidak ada |
| `Admin/AuthController.php` | `showLoginForm()` | Return form login admin | `admin.login` | ✅ ada | ❌ tidak ada |
| | `login(Request)` | Validasi username/password; `Admin::where('username')->first()` + `Hash::check()` (bukan `Auth::attempt()` karena tabel `admin` custom, bukan `users`); `session()->regenerate()`; simpan `session(['admin_id','admin_nama'])`; redirect ke `admin.dashboard` | — | — | ❌ tidak ada |
| | `logout(Request)` | `session()->forget([...])` + `invalidate()` + `regenerateToken()` → redirect `admin.login` | — | — | ❌ tidak ada |
| `Admin/DashboardController.php` | `index()` | 4 angka: `jumlah_pesanan_hari_ini`, `jumlah_pesanan_diproses`, `pendapatan_hari_ini` (hanya `status_pembayaran='terverifikasi'`), `jumlah_produk_sold_out` | `admin.dashboard` | ✅ ada (**tapi tidak memakai variabel**) | ❌ tidak ada |
| `Admin/KasirController.php` | `index()` | Kirim produk aktif, komponen aktif (sayur/protein), dan daftar cabang | `admin.kasir` | ✅ ada (**tapi tidak memakai variabel**) | ❌ tidak ada |
| | `store(Request)` | Transaksi offline: `jenis='kasir'`, `id_admin` dari `session('admin_id')`, `status_pembayaran='terverifikasi'`, `metode_ambil='pickup'`, ongkir 0, harga dihitung dari **`harga_offline`** (beda dari customer yang pakai `harga_online`), `DB::transaction()` | redirect `admin.kasir` | ✅ ada (**tapi tidak memakai variabel**) | ❌ tidak ada |
| | `generateKodePesanan()` (private) | Sama format dengan `PesananController` | — | — | — |
| `Admin/LaporanController.php` | `index(Request)` | Filter `tanggal_mulai`/`tanggal_selesai` (default bulan berjalan), agregasi `detail_pesanan` per `nama_item` (`SUM(jumlah)`, `SUM(subtotal_item)`) dengan `whereHas('pesanan')` + filter `status_pembayaran='terverifikasi'` dan `status_item='aktif'`, urut `total_terjual` desc | `admin.laporan` | ❌ tidak ada | ❌ tidak ada |
| `Admin/MenuController.php` | `index()` | Semua produk (aktif & nonaktif) + `with('cabang')` | `admin.menu.index` | ❌ tidak ada (yang ada: `admin/kelola_menu.blade.php`) | ❌ tidak ada |
| | `create()` | Form tambah produk | `admin.menu.create` | ❌ tidak ada | ❌ tidak ada |
| | `store(Request)` | Validasi nama/harga/foto/dll → `Produk::create()` | redirect `admin.menu.index` | — | ❌ tidak ada |
| | `edit($id)` | `Produk::findOrFail()` → form edit | `admin.menu.edit` | ❌ tidak ada | ❌ tidak ada |
| | `update(Request,$id)` | Validasi + `update()` | redirect `admin.menu.index` | — | ❌ tidak ada |
| | `destroy($id)` | Hard delete `Produk::delete()` | redirect `admin.menu.index` | — | ❌ tidak ada |
| `Admin/PesananController.php` | `index()` | Semua pesanan (online & kasir) `with(['cabang','detailPesanan'])` + `latest()` | `admin.pesanan.index` | ❌ tidak ada (yang ada: `admin/daftar_pesanan.blade.php`) | ❌ tidak ada |
| | `konfirmasiOngkir(Request,$id)` | Validasi `ongkir`; hanya untuk `metode_ambil='delivery'`; update `ongkir` + `status_ongkir='terkonfirmasi'` + hitung ulang `total_bayar` | redirect `admin.pesanan.index` | — | ❌ tidak ada |
| | `updateStatus(Request,$id)` | Validasi `status_pesanan` ∈ (diproses, siap, selesai, batal) → update seluruh pesanan | redirect `admin.pesanan.index` | — | ❌ tidak ada |
| | `batalkanItem(Request,$id_detail)` | Update `status_item='dibatalkan'` pada **satu** baris detail (bukan seluruh pesanan) | redirect `admin.pesanan.index` | — | ❌ tidak ada |
| `Admin/PromoController.php` | `index()` | Semua promo | `admin.promo.index` | ❌ tidak ada (yang ada: `admin/promo.blade.php`) | ❌ tidak ada |
| | `create()` | Form tambah promo | `admin.promo.create` | ❌ tidak ada | ❌ tidak ada |
| | `store(Request)` | Validasi `tipe_potongan` ∈ (nominal, persen, gratis_ongkir); `nilai_potongan` wajib kecuali gratis_ongkir; `batas_penggunaan` int > 0 → `Promo::create()` | redirect `admin.promo.index` | — | ❌ tidak ada |
| | `edit($id)` | Form edit promo | `admin.promo.edit` | ❌ tidak ada | ❌ tidak ada |
| | `update(Request,$id)` | Validasi sama + update + `nonaktifkanOtomatis()` | redirect `admin.promo.index` | — | ❌ tidak ada |
| | `destroy($id)` | Kalau promo sudah dipakai (`$promo->pesanan()->exists()`) → **nonaktifkan** (bukan hapus,FK); kalau belum dipakai → hard delete | redirect `admin.promo.index` | — | ❌ tidak ada |
| | `nonaktifkanOtomatis(Promo)` (private) | Auto nonaktif jika `jumlah_terpakai >= batas_penggunaan`. Dipanggil dari `update()`. **Saran di komentar file**: idealnya juga dipanggil dari `PesananController`/`KasirController` saat promo dipakai — saat ini belum ada jalur itu (dan method private tidak bisa dipanggil controller lain) | — | — | — |

**Ringkasan bagian 5:** 11 kelas controller (4 customer + 7 admin) sudah ada, **0 yang terhubung ke route**. Yang sudah “nyambung” hanya ke **model & database** (sudah diuji jalan, mis. query `LaporanController` diverifikasi benar: `terjual=3, pendapatan=45000`).

---

## 6. Routing

### 6.1 Isi lengkap `routes/web.php` saat ini (copy apa adanya)

```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('admin.login');
});

Route::get('/admin/kasir', function () {
    return view('admin.kasir');
});

Route::get('/admin/promo', function () {
    return view('admin.promo');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/admin/daftar-pesanan', function () {
    return view('admin.daftar_pesanan');
});

Route::get('/admin/pengaturan', function () {
    return view('admin.pengaturan');
});

Route::get('/admin/kelola-menu', function () {
    return view('admin.kelola_menu');
});
```

### 6.2 Analisa routing

**Semua route saat ini adalah closure `return view(...)` langsung — tidak ada satu pun yang memanggil controller:**

| Method | URI | Closure return | View | Controller |
|---|---|---|---|---|
| GET | `/` | `view('welcome')` | `welcome` | ❌ |
| GET | `/login` | `view('admin.login')` | `admin.login` | ❌ (padahal `Admin\AuthController::showLoginForm()` sudah ada) |
| GET | `/admin/kasir` | `view('admin.kasir')` | `admin.kasir` | ❌ (`Admin\KasirController` ada) |
| GET | `/admin/promo` | `view('admin.promo')` | `admin.promo` | ❌ (`Admin\PromoController` ada) |
| GET | `/admin/dashboard` | `view('admin.dashboard')` | `admin.dashboard` | ❌ (`Admin\DashboardController` ada) |
| GET | `/admin/daftar-pesanan` | `view('admin.daftar_pesanan')` | `admin.daftar_pesanan` | ❌ (`Admin\PesananController` ada) |
| GET | `/admin/pengaturan` | `view('admin.pengaturan')` | `admin.pengaturan` | ❌ (belum ada controller pengaturan toko sama sekali) |
| GET | `/admin/kelola-menu` | `view('admin.kelola_menu')` | `admin.kelola_menu` | ❌ (`Admin\MenuController` ada) |

**Controller/method yang sudah dibuat tapi BELUM punya route sama sekali (semua):**

- `ProdukController@index`, `ProdukController@show`
- `KomponenSaladController@index`
- `PesananController@store`, `PesananController@show`
- `RatingController@store`
- `Admin\AuthController@showLoginForm`, `@login`, `@logout`
- `Admin\DashboardController@index`
- `Admin\KasirController@index`, `@store`
- `Admin\LaporanController@index`
- `Admin\MenuController@index`, `@create`, `@store`, `@edit`, `@update`, `@destroy`
- `Admin\PesananController@index`, `@konfirmasiOngkir`, `@updateStatus`, `@batalkanItem`
- `Admin\PromoController@index`, `@create`, `@store`, `@edit`, `@update`, `@destroy`

**Nama route yang dipanggil controller tapi belum didefinisikan** (akan throw `RouteNotFoundException` kalau controller dipanggil):
`pesanan.show`, `admin.dashboard`, `admin.login`, `admin.kasir`, `admin.menu.index`, `admin.promo.index`, `admin.pesanan.index`.

**Catatan lain:**
- Tidak ada route bernama sama sekali → `Route::has('login')` = false, `Route::has('register')` = false (dicek via `Route::has`). `welcome.blade.php` memanggil `route('login')` tapi blok itu berada di dalam `@if (Route::has('login'))` sehingga **tidak error** — semua 8 route yang ada diuji dan mengembalikan **HTTP 200**.
- `php artisan route:list` total 12 route: 8 di atas + `GET /up` (health check) + `POST _boost/browser-logs` (Laravel Boost) + `GET/PUT storage/{path}` (Laravel local storage).
- `bootstrap/app.php`: `withMiddleware()` **kosong** → tidak ada alias middleware, tidak ada group route, tidak ada proteksi admin.
- Hanya `routes/web.php` yang terdaftar (plus `routes/console.php` default). Tidak ada `routes/api.php`.

---

## 7. View/Blade

Semua file `.blade.php` di `resources/views/` (**8 file**):

### 7.1 Customer-facing

| File | Isi | Status |
|---|---|---|
| `resources/views/welcome.blade.php` (223 baris) | **Skeleton default Laravel 13**, bukan desain Hamta. Mengandung `@if (Route::has('login'))`, `route('login')`/`route('register')` (di guarded block), `@fonts`, fallback Tailwind inline. | Hardcode total; `{{ }}` yang ada hanya `app()->getLocale()` & `config('app.name')`. **Bukan landing page Hamta** — desain Landing Page ada di Figma (menurut DESIGN.md) tapi blade-nya belum dibuat. |

**Total halaman customer yang ada: 0 (nol).** Tidak ada blade untuk: landing page Hamta, katalog/menu, detail produk, kreasikan salad, checkout, status pesanan, form rating.

### 7.2 Admin

Semua blade admin memakai pola identik: HTML utuh dari `<!DOCTYPE html>` (tanpa `@extends`/`@section`/layout bersama), sidebar `#0B412B` "Hamta Panel" dengan 6 menu (**semua `href="#"`**) — kecuali `admin/login.blade.php` yang tidak punya sidebar (0 `href="#"`); topbar dengan chip "Yunita", Google Fonts Plus Jakarta Sans, `@vite` di dalam `@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))`, dan komponen `<x-lucide-*>`. **Tidak ada satu pun blade admin yang punya `@foreach`, `{{ $var }}`, atau `@php`** — semuanya 100% data dummy hardcode.

| File | Baris | Konten | Data dummy yang dipakai |
|---|---|---|---|
| `admin/login.blade.php` | 178 | Split layout (panel kiri `#0B412B` + form kanan), logo `public/images/hamta-logo-white.png`, field username/password, **kotak reCAPTCHA palsu** (SVG statis, bukan widget Google), tombol "Masuk" | `<form class="space-y-4" onsubmit="return false;">` — **tanpa `action`, tanpa `@csrf`, tanpa `method="POST"`**; kedua input `value="Admin"` dan `readonly`; tombol `type="button"` → form login **tidak pernah terkirim** |
| `admin/dashboard.blade.php` | 304 | 4 stat card, panel chart penjualan 7 hari, panel notifikasi | Stat card: `24`, `Rp 3.500.000`, `6`, `24`. Chart = **SVG inline statis** (grid Y 0–400, polyline 7 titik) — **bukan Chart.js**. Notifikasi: 5 item identik "Jasmine Olivia Putri — 1x Salad Original + 2x Sandwich — 10:24 AM" |
| `admin/kelola_menu.blade.php` | 292 | Search (readonly) + 4 filter pill + tabel | Tabel kolom Foto/Menu/Kategori/Harga/Aksi dengan **4 baris identik**: "Salad Original / Salad / Rp 15.000"; tombol edit (oranye `#E08A3C`) & hapus (merah `#E05C5C`) = `type="button"` tanpa aksi |
| `admin/daftar_pesanan.blade.php` | 261 | Search + filter + tabel | Tabel No. Pesanan/Customer/Cabang/Total/Aksi, **4 baris**: `#1959 Jasmine Olivia Lidah Kulon Rp 45.000`, `#1959` (duplikat), `#1958 Budi Santoso Merr Rp 75.000`, `#1957 Siti Rahma Lidah Kulon Rp 30.000`; tombol "Detail" tanpa aksi/target |
| `admin/kasir.blade.php` | 302 | POS 2 kolom: katalog + keranjang | Filter pill (Semua/Salad/Sandwich/Wrap/Jus/Ricebowl), **6 product card identik** "Salad Enak / Rp 25.000", keranjang berisi 2 item "Salad Enak" + tombol checkout |
| `admin/promo.blade.php` | 248 | Search + "Tambah Promo" + tabel | Tabel Promo/Status/Aksi, **4 baris identik** "Potongan Rp 500 pembelian minimal 50"; toggle switch = div statis (bukan input), tombol edit/hapus tanpa aksi |
| `admin/pengaturan.blade.php` | 265 | Section "Logo dan Identitas Usaha", "Kontak dan Media Sosial" (input WhatsApp/Instagram), "Cabang" | 2 kartu cabang hardcode "Lidah Kulon" & "Merr"; **tidak ada `<form>`/action** → tidak ada penyimpanan |

### 7.3 Blade yang SUDAH ada di desain (Figma) tapi file-nya BELUM dibuat

- **Semua halaman customer** (Landing Page, Katalog/Menu, Detail Produk, Kreasikan Salad, Checkout, Status Pesanan, Rating/Ulasan).
- **Admin: Laporan** (A6) — `Admin\LaporanController` sudah ada, view `admin.laporan` belum.
- **Admin: form tambah/edit** — `admin.menu.create`, `admin.menu.edit`, `admin.promo.create`, `admin.promo.edit`, form create produk, form detail pesanan admin, form edit cabang di Pengaturan Toko.

### 7.4 Ketidaksesuaian nama view ↔ file blade

| ControllerQty | View yang dipanggil | File yang ada | Status |
|---|---|---|---|
| `Admin\MenuController` | `admin.menu.index` / `.create` / `.edit` | `admin/kelola_menu.blade.php` | ❌ nama beda |
| `Admin\PromoController` | `admin.promo.index` / `.create` / `.edit` | `admin/promo.blade.php` | ❌ nama beda |
| `Admin\PesananController` | `admin.pesanan.index` | `admin/daftar_pesanan.blade.php` | ❌ nama beda |
| `Admin\LaporanController` | `admin.laporan` | — | ❌ tidak ada |
| `Admin\DashboardController` | `admin.dashboard` | ✅ ada | ✅ tapi statis |
| `Admin\KasirController` | `admin.kasir` | ✅ ada | ✅ tapi statis |
| `Admin\AuthController` | `admin.login` | ✅ ada | ✅ tapi form tidak berfungsi |
| `PesananController@show`, `RatingController@store` | `status-pesanan` | — | ❌ tidak ada |
| `ProdukController` | `katalog`, `produk-detail` | — | ❌ tidak ada |
| `KomponenSaladController` | `kreasikan-salad` | — | ❌ tidak ada |

---

## 8. Gap Analysis

### 8.1 Peta sitemap yang dipakai sebagai acuan

**Tidak ada file sitemap di repo ini.** Acuan yang tersedia:
- `PRD.md` §5.1: fitur customer **F1–F7** (Katalog, Kreasikan Salad, Checkout, Promo, QRIS, Status Pesanan, Rating).
- `PRD.md` §5.2: halaman admin **A1–A7** (Dashboard, Kelola Menu, Kasir, Daftar Pesanan, Promo, Laporan, Pengaturan Toko).
- `DESIGN.md` §6: menyebut **"Customer (8 halaman)"** dan **"Admin (7 halaman)"** → total 15 halaman, **bukan 13**.
- **Discrepancy yang perlu dikonfirmasi tim:** customer F1–F7 = 7 fitur, tapi DESIGN.md menyebut 8 halaman customer; halaman ke-8 kemungkinan "Landing Page" (tidak bernomor F). ADMIN A1–A7 = 7 halaman sudah cocok. Jadi total yang konsisten = **8 customer + 7 admin = 15 halaman**.
- Use case diagram: `PRD.md` §12 menyebut diagram UML & ERD "dokumentasi terpisah, sudah selesai" — **filenya tidak ada di repo ini**, jadi tidak bisa diverifikasi dari kode. Acuan closest = `schema.sql` (hasil translate ERD).

### 8.2 GAP TOTAL: belum ada sama sekali

| No | Gap | Bukti | Dampak |
|---|---|---|---|
| G1 | **Seluruh halaman customer (0 dari 7–8)** | `resources/views/` hanya `welcome.blade.php` + `admin/` | Fitur F1–F7 tidak punya UI sama sekali; website customer tidak bisa dipakai |
| G2 | **Semua route controller (11 kelas, 25+ method)** | `routes/web.php` 100% closure | Semua controller mati; tidak ada endpoint checkout, rating, kasir, laporan, promo, menu |
| G3 | **Middleware proteksi admin** | `app/Http/Middleware/` **foldernya tidak ada**; `bootstrap/app.php` `withMiddleware()` kosong | Halaman admin & endpoint kasir/promo/menu tanpa proteksi session |
| G4 | **`Admin\LaporanController` tanpa view & route** | view `admin.laporan` tidak ada | Fitur A6 (Laporan) tidak bisa diakses walau query-nya sudah siap & teruji |
| G5 | **Seeder Produk** | `database/seeders/` tidak ada `ProdukSeeder`; `produk` = 0 baris | Katalog kosong, kasir tanpa menu, custom salad tidak punya harga dasar |
| G6 | **Seeder Promo** | `promo` = 0 baris | Fitur A5/F4 tanpa data awal |
| G7 | **Logika penerapan promo di checkout** | `PesananController` & `KasirController` selalu `total_diskon = 0`; tabel `pesanan_promo` tidak pernah diisi; `promo.jumlah_terpakai` tidak pernah naik | F4 (Terapkan Promo) & A5 belum berfungsi sama sekali; `nonaktifkanOtomatis()` di `PromoController` tidak punya jalur pemicu |
| G8 | **reCAPTCHA v2** | Tidak ada `config/recaptcha.php`, tidak ada env key, tidak ada middleware | NF4 belum terpenuhi; form login & checkout belumDilindungi |
| G9 | **Upload bukti bayar & QRIS statis** | `PesananController::store()` hanya menyimpan string `bukti_bayar` dari request; tidak ada logic upload/`Storage::put`, tidak ada QRIS image/pengaturan | F5 belum lengkap; saat ini path bukti bayar harus dikirim manual oleh frontend |
| G10 | **CRUD admin untuk Komponen Salad & Bahan** | Hanya `KomponenSaladController::index()` (customer) & `KomponenSaladController` admin tidak ada; `Bahan` tidak ada controller admin | A2 (Kelola Menu: komponen + daftar bahan) belum terpenuhi |
| G11 | **Backend "Pengaturan Toko" (A7)** | `admin/pengaturan.blade.php` hardcode, tanpa form/action; `schema.sql` pun tidak punya tabel pengaturan | Data jam/alamat cabang tidak bisa diedit dari UI |
| G12 | **Notifikasi real-time / WA** | Tidak ada service/command/queue job | Notifikasi admin & pelanggan belum ada |
| G13 | **Chart.js** | Tidak ada di package.json/node_modules | Grafik dashboard & laporan harus pakai SVG manual atau Chart.js belum terpasang |
| G14 | **Pencarian, filter, pagination** | Kolom "Cari Menu"/"Cari Pesanan"/"Cari Promo" di blade berupa input `readonly`; controller tidak punya query filter/paginate | UI belum interaktif untuk data besar |
| G15 | **Notifikasi flash (success/error)** | Semua controller pakai `->with('success')`/`->with('error')`, tapi tidak ada blade yang menampilkannya | Pesan selalu tak terlihat oleh user |
| G16 | **Komponen JS (Alpine.js dll.)** | `resources/js/app.js` kosong | Keranjang, filter pill, toggle promo, tab checkout tidak punya interaksi |
| G17 | **Paginasi & layout bersama** | 7 blade admin mengulang sidebar/topbar manual, tanpa `@extends`/`@include` | Perbaikan design system harus diulang 7× (DESIGN.md §4.7/4.8 minta komponen reusable) |
| G18 | **Tests** | Hanya `tests/Feature/ExampleTest.php` & `tests/Unit/ExampleTest.php` (default Laravel) | Tidak ada coverage untuk validasi harga backend |

### 8.3 Sudah ada tapi BELUM nyambung satu sama lain

| # | Yang sudah ada | Yang belum nyambung | Perbaikan |
|---|---|---|---|
| N1 | 11 controller (customer + admin) | Tidak ada route | Tulis `routes/web.php` dengan nama route yang sudah dipakai controller (`pesanan.show`, `admin.*`) |
| N2 | 7 blade admin dengan desain rapi | Semuanya hardcode, tidak ada `@foreach`/variabel | Ubah tiap blade agar menerima data dari controller yang sudah ada |
| N3 | 8 route closure | Mengarah ke view statis, bukan controller | Arahkan ke controller (`Admin\DashboardController@index`, dst.) |
| N4 | `Admin\DashboardController` kirim 4 variabel | `admin/dashboard.blade.php` tidak memakainya (angka statis 24 / Rp 3.500.000 / 6 / 24) | Ganti angka statis dengan `{{ $jumlah_pesanan_hari_ini }}` dll. |
| N5 | `Admin\KasirController` kirim produk/komponen/cabang | `admin/kasir.blade.php` tetap 6 card "Salad Enak" | Loop `{{ $produk }}` |
| N6 | Nama view di controller | Nama file blade berbeda (`admin.menu.index` vs `kelola_menu`) | Putuskan 1 konvensi (rekomendasi: samakan dengan nama file yang sudah ada, atau rename file) |
| N7 | `Admin\AuthController` simpan `session('admin_id')` | Tidak ada middleware yang memakainya; `Admin\KasirController` satu-satunya yang memakainya (dengan guard manual) | Buat `EnsureAdminLogin` + alias `admin` di `bootstrap/app.php` |
| N8 | `Admin\PesananController` (status/ongkir/batal item) | `admin/daftar_pesanan.blade.php` tombol "Detail" tanpa form/action | Isi form POST + CSRF |
| N9 | `Admin\PromoController` | `admin/promo.blade.php` toggle status = div statis, tombol edit/hapus tanpa aksi | Ubah toggle jadi form PATCH |
| N10 | `Admin\MenuController` | `admin/kelola_menu.blade.php` tombol "Tambah Menu"/edit/hapus tanpa aksi | Arahkan ke route controller, buat form create/edit |
| N11 | `Admin\LaporanController` (query sudah teruji jalan) | View + route belum ada; data kosong (0 pesanan) | Buat view + route, isi data dulu (G5) |
| N12 | `PesananController@show` + `RatingController@store` | Butuh view `status-pesanan` yang belum ada | Buat blade status pesanan |
| N13 | `KomponenSaladController@index` (customer) | View `kreasikan-salad` belum ada; `KomponenSalad` tidak punya `produk` dasar | Buat view + seed produk dasar |
| N14 | `ProdukController` | View `katalog` & `produk-detail` belum ada | Buat blade customer |
| N15 | `Admin\AuthController::login` set session | `Admin\AuthController::login` redirect ke `admin.dashboard` (route belum ada) → akan error | Define route dulu |
| N16 | `PromoController::nonaktifkanOtomatis()` | Private & hanya dipanggil dari `update()`; tidak pernah terpicu dari pemakaian promo | Pindahkan ke model `Promo` (mis. `catatPakai()`) |
| N17 | `Cabang` model + `CabangSeeder` | `$fillable` typo → `layani_pickup`/`layani_dinein` tidak pernah tersimpan | Perbaiki `$fillable` (atau gunakan `$guarded = []`) |
| N18 | Migration `rating` | Tidak sinkron dengan `schema.sql` (CHECK constraint hilang) | Migration baru untuk menambah CHECK |
| N19 | `public/hot` ada, `public/build` tidak | Kalau dev server mati → aset gagal dimuat | `npm run build` untuk produksi lokal |
| N20 | `DESIGN.md` §2 palet warna | `app.css` tidak punya token warna; warna hardcode hex di tiap blade | Tambahkan `@theme` token warna |
| N21 | `app.css` & vite → Instrument Sans; blade → Plus Jakarta Sans | Dua font berbeda aktif bersamaan | Putuskan satu font |
| N22 | `APP_NAME=Laravel` | Judul tab & nama brand masih "Laravel" | Ubah `.env` |

### 8.4 Risiko logika yang sudah tertulis di kode (berkas)

- `PesananController` & `KasirController`: item custom wajib `id_produk` (produk dasar) — **belum ada produk "Kreasikan Salad Sendiri"**, dan `harga_tambahan` komponen masih 0 → harga custom = 0 (memang sudah dicegah user: `total_bayar` dihitung di backend).
- `PesananController::store()`: `ongkir` delivery = NULL dan `total_bayar` = subtotal (belum termasuk ongkir) → harus di-update admin saat ongkir dikonfirmasi (sudah dilakukan di `Admin\PesananController::konfirmasiOngkir()`).
- `Admin\PesananController::batalkanItem()` tidak mengitung ulang `subtotal`/`total_bayar` pesanan.
- `LaporanController`: `total_pendapatan` = sum `subtotal_item` (belum dikurangi diskon promo, belum termasuk ongkir).
- Tidak ada casting decimal di model → hasil `sum()` berupa string ("45000.00").

---

## 9. Rekomendasi Urutan Kerja Selanjutnya

Urutan memperhitungkan dependency teknis (migration → model → controller → route → view) dan urutan nilai yang bisa dipakai/demo duluan.

**Tahap 0 — Rapikan fondasi & data (blocking semua tahap lain)**
1. Perbaiki `$fillable` `Cabang` → `layani_pickup`/`layani_dinein` (bug data yang aktif). Re-seed cabang.
2. Migration baru untuk sinkronkan `rating` dengan `schema.sql` (tambah CHECK bintang 1–5) — jangan edit migration yang sudah Ran.
3. Tambah `@theme` token warna Hamta di `resources/css/app.css` (hemat pekerjaan rewriting 7 blade).
4. Putuskan font: samakan Instrument Sans (vite) vs Plus Jakarta Sans (blade) → 1 sumber.
5. Ubah `APP_NAME` di `.env` → "Hamta Healthy Food".

**Tahap 1 — Data master (blocking katalog, kasir, laporan)**
6. `ProdukSeeder` (menu reguler + harga offline/online + `bisa_dikustom` untuk produk dasar "Kreasikan Salad Sendiri") dan isi `produk_bahan`.
7. `PromoSeeder` (1–2 contoh promo).
8. **Tugas wajib ke owner (Kak Yunita):** konfirmasi `harga_tambahan` komponen, `harga_offline`/`harga_online` produk, info alergen `bahan.keterangan`, alamat lengkap cabang MERR, jam operasional per cabang.

**Tahap 2 — Routing & proteksi admin (blocking semua controller)**
9. `EnsureAdminLogin` middleware + alias `admin` di `bootstrap/app.php`.
10. Tulis ulang `routes/web.php`: group `admin` dengan middleware `admin`, definisikan **semua nama route yang sudah dipakai controller** (`pesanan.show`, `admin.dashboard`, `admin.login`, `admin.kasir`, `admin.menu.index`, `admin.promo.index`, `admin.pesanan.index`) + route customer (`katalog`, `produk-detail`, `kreasikan-salad`, `checkout`, `pesanan.show`, `rating.store`).
11. Putuskan konvensi nama view: samakan nama file blade dengan nama view yang dipanggil controller (atau sebaliknya) agar tidak ada 404 `View [...] not found`.

**Tahap 3 — View layer admin (7 blade statis → dinamis)**
12. Extract layout bersama: `resources/views/components/admin/layout.blade.php` (sidebar + topbar) → dipakai 7 halaman; session flash (`success`/`error`) ditampilkan sebagai toast di layout.
13. Ubah `admin/dashboard.blade.php` memakai 4 variabel dari `DashboardController`; ganti SVG chart dengan data nyata (install `chart.js` via npm bila memang mau Chart.js).
14. Ubah `admin/kelola_menu.blade.php` looping `$produk`, form create/edit, toggle sold-out.
15. Ubah `admin/daftar_pesanan.blade.php` looping `$pesanan`, form update status/konfirmasi ongkir/batalkan item.
16. Ubah `admin/kasir.blade.php` looping `$produk`/`$sayuran`/`$protein`/`$cabang` + form transaksi (butuh JS/Alpine untuk keranjang).
17. Ubah `admin/promo.blade.php` looping `$promo` + toggle status + form create/edit.
18. Buat `admin/laporan.blade.php` (table + filter periode) → pasangkan dengan `Admin\LaporanController` yang sudah siap.
19. Buat `admin/pengaturan.blade.php` yang benar-benar menyimpan (butuh tabel/setting baru → **perrocrn migration baru**, acuan `schema.sql` perlu diperbarui; atau sementara: simpan ke tabel `cabang` yang sudah ada).

**Tahap 4 — Customer-facing (0 dari 7–8 halaman — porsi terbesar)**
20. Landing page + komponen `navbar`/`footer` (DESIGN.md §4.1/4.6) + card produk (§4.2).
21. Katalog (`ProdukController@index`) + detail produk (`@show`) → komponen card produk dipakai ulang.
22. Kreasikan Salad (`KomponenSaladController@index`) — 2 section (£sayuran/protein) + hitung harga live di frontend (JS) tetapi **harga final tetap divalidasi backend**.
23. Checkout guest + keranjang (Alpine/JS) → `PesananController@store` (kontrak input `cart[].tipe/id_produk/id_komponen/jumlah` sudah jelas di kode).
24. Halaman status pesanan (`PesananController@show`) → juga jadi tempat form rating (`RatingController@store`).
25. Terapkan promo di checkout (F4): service hitung promo yang cocok + stacking → isi `pesanan_promo`, naikkan `jumlah_terpakai`, panggil auto-nonaktif (pindahkan logic ke model `Promo`).

**Tahap 5 — Hardening**
26. reCAPTCHA v2 (middleware + `config/recaptcha.php` + key di `.env`) di checkout & login.
27. Upload bukti bayar (controller/JS) + QRIS statis (upload image, simpan path).
28. Filter/paginasi/search pada daftar pesanan, menu, laporan.
29. `npm run build` (produksi lokal) + `php artisan config:cache`.
30. Test manual alur ujung-ke-ujung + tulis test Feature untuk validasi harga backend (satu test paling bernilai: harga dihitung dari DB, bukan dari input).

---

## Lampiran A — Catatan verifikasi yang dilakukan

- Semua file migration, model, seeder, controller, view, route, config, dan dokumen (PRD/DESIGN/AGENTS/CLAUDE/README/schema.sql) dibaca langsung.
- `php artisan migrate:status` → 16 migration, semua Ran (batch 1).
- Query `SELECT COUNT(*)` per tabel → jumlah baris aktual (lihat §4.2).
- Simulasi request via kernel Laravel ke 8 route existing → semua **HTTP 200**.
- `Route::has('login')` / `Route::has('register')` → **false** (tidak ada route bernama).
- Query agregasi `Admin\LaporanController` diuji dengan data sementara dalam transaksi lalu **rollback** → `terjual=3, pendapatan=45000` (tabel kembali 0 baris).
- Mass-assignment `Cabang` diuji dengan transaksi + rollback → `layani_dinein` tersimpan **0** meski input `true` (konfirmasi bug `$fillable`).
- `php -l` dijalankan untuk semua controller yang dibuat sesi ini (Produk, KomponenSalad, Pesanan, Rating, Admin/*) → tidak ada syntax error.
- Status Git: `feat/admin`, HEAD `f74b035`; 5 path controller masih untracked.

## Lampiran B — Peta cepat nama route/view yang dibutuhkan

| Dipakai di kode | Harus dibuat |
|---|---|
| `route('pesanan.show', ['kode_pesanan' => ...])` | route named `pesanan.show` (GET) |
| `route('admin.dashboard')` | route named `admin.dashboard` |
| `route('admin.login')` | route named `admin.login` (GET + POST `admin.login` untuk submit) |
| `route('admin.kasir')` | route named `admin.kasir` (GET + POST) |
| `route('admin.menu.index')` | route named `admin.menu.index` (+ `admin.menu.create`, `admin.menu.edit`) |
| `route('admin.promo.index')` | route named `admin.promo.index` (+ `admin.promo.create`, `admin.promo.edit`) |
| `route('admin.pesanan.index')` | route named `admin.pesanan.index` (+ endpoint konfirmasi ongkir / update status / batal item) |
| `view('katalog')`, `view('produk-detail')`, `view('kreasikan-salad')`, `view('status-pesanan')`, `view('admin.laporan')` | file blade baru (belum ada) |
| `view('admin.menu.*')`, `view('admin.promo.*')`, `view('admin.pesanan.index')` | file blade baru atau rename file yang ada (`kelola_menu`, `promo`, `daftar_pesanan`) |
