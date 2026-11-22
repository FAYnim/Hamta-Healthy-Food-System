# Hamta Healthy Food — Website & Sistem Kasir (POS)

Proyek **PjBL (Project Based Learning)** kelas XI RPL 1, Kelompok 2, SMK Negeri 1 Surabaya.

Aplikasi web all-in-one untuk UMKM **Hamtash Salad** (brand "Hamta Healthy Food", owner: Kak Yunita) di Surabaya. Terdiri dari dua sisi:

- **Customer-facing** — katalog menu digital, kustomisasi salad, keranjang, dan checkout mandiri tanpa login.
- **Admin panel** — dashboard, kasir offline, kelola pesanan, kelola menu, promo, laporan, dan pengaturan toko.

Tujuan utama: pelanggan bisa melihat katalog & harga secara transparan dan **memesan langsung lewat website** (checkout mandiri, QRIS), bukan sekadar diarahkan ke WhatsApp atau aplikasi pihak ketiga.

---

## Fitur yang Sudah Diimplementasikan

### Sisi Customer (guest, tanpa akun)

| Fitur | Halaman / Route |
|---|---|
| Landing page dengan 8 menu terbaru | `/` |
| Katalog menu + best seller + filter per kategori (Salad, Sandwich, Juice) | `/daftar-menu` |
| Detail produk (modal, endpoint JSON) | `/produk/{id}` |
| "Kreasikan Salad Sendiri" — pilih base salad + komponen sayuran/protein | `/kreasikan-salad` |
| Keranjang belanja di sisi browser | — |
| Checkout guest: pickup/delivery, pilih cabang, alamat, ongkir flat | `/checkout` |
| Pembayaran QRIS Midtrans (QR otomatis) dengan **fallback QRIS statis** + upload bukti transfer | `/checkout` |
| Pembayaran tunai (tercatat, diverifikasi admin) | `/checkout` |
| Lacak status pesanan lewat kode pesanan + polling status live | `/status-pesanan/{kode}` |
| Countdown kedaluwarsa QRIS + upload ulang bukti bayar | `/status-pesanan/{kode}` |
| Rating & ulasan setelah pesanan selesai | `/status-pesanan/{kode}` |
| Halaman "Cerita Kami", alamat, dan pembayaran | `/cerita-kami`, `/alamat`, `/pembayaran` |
| Diskon/promo otomatis (persen, nominal, gratis ongkir, syarat min. porsi, kuota) | diterapkan saat checkout |

### Sisi Admin (login manual + reCAPTCHA v2)

| Fitur | Route |
|---|---|
| Login/logout admin | `/admin/login`, `/admin/logout` |
| Dashboard: pesanan hari ini, pendapatan, grafik 7 hari (Chart.js), produk sold out | `/admin/dashboard` |
| Kasir offline (input transaksi walk-in, termasuk salad custom) | `/admin/kasir` |
| Daftar pesanan: detail, ubah status, verifikasi pembayaran, konfirmasi ongkir, batalkan item | `/admin/daftar-pesanan` |
| Kelola menu: CRUD produk, toggle status aktif/sold out, CRUD komponen salad | `/admin/kelola-menu` |
| Kelola promo: CRUD, toggle status, kuota otomatis nonaktif saat habis | `/admin/promo` |
| Laporan penjualan per item dengan filter rentang tanggal | `/admin/laporan` |
| Pengaturan: identitas toko, QRIS statis, kontak, tarif ongkir, CRUD cabang | `/admin/pengaturan` |

### Belum Ada / Di Luar Scope Saat Ini

- Login/registrasi customer (memang didesain guest checkout).
- reCAPTCHA baru dipasang di login admin; `RECAPTCHA_*` sudah ada di config tetapi belum dipakai di form checkout.
- Cloud storage (masih local disk `storage/app/public`).
- Notifikasi WhatsApp/email otomatis.

---

## Tech Stack

Diambil dari `composer.json`, `package.json`, `vite.config.js`, dan `config/`:

- **Bahasa**: PHP `^8.3`
- **Framework**: Laravel `^13.17` (monolith, Blade)
- **Database**: MySQL (`.env` lokal memakai `DB_CONNECTION=mysql`; default config Laravel masih `sqlite`, dan `.env.example` masih `sqlite` — sesuaikan sendiri)
- **Frontend**: Blade + Tailwind CSS v4 (`@tailwindcss/vite`) + Vite 8
- **Chart**: Chart.js `^4.6`
- **Icon**: Lucide via `mallardduck/blade-lucide-icons`
- **Payment**: `midtrans/midtrans-php` `^2.3` (QRIS Core API) + QRIS statis manual
- **Captcha**: Google reCAPTCHA v2 (login admin)
- **Auth**: manual, session-based, middleware `admin.auth` — tanpa Breeze/Jetstream
- **Testing**: PHPUnit `^12.5`
- **Tooling dev**: Laravel Boost, Pail, Pint

---

## Struktur Folder

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Auth, Dashboard, Kasir, Pesanan, Menu, Promo, Laporan, Pengaturan
│   │   ├── Customer/       # Checkout, StatusPesanan
│   │   ├── MidtransNotificationController.php
│   │   └── RatingController.php
│   └── Middleware/         # AdminAuthenticate (guard session admin)
├── Models/                 # Produk, Pesanan, DetailPesanan, Promo, Cabang, Pengaturan, Rating, dll.
└── Services/               # MidtransService (charge QRIS, verifikasi signature), PromoService (hitung diskon)

database/
├── migrations/             # Skema tabel (cabang, admin, produk, pesanan, promo, rating, pengaturan, ...)
└── seeders/                # AdminSeeder, CabangSeeder, BahanSeeder, KomponenSaladSeeder, ProdukSeeder

resources/
├── css/app.css             # Tailwind entrypoint
├── js/app.js               # Chart.js dashboard
└── views/
    ├── admin/              # Halaman admin + partial sidebar
    ├── customer/           # Katalog, checkout, status pesanan, cerita, dll.
    ├── components/         # Komponen Blade reusable
    └── welcome.blade.php   # Landing page

routes/web.php              # Seluruh route customer + admin
public/                     # Asset publik (gambar, favicon, hasil build Vite)
tests/Feature/              # Test fitur (auth admin, promo, ongkir, QRIS, Midtrans, black-box)
tests/Unit/                 # Test unit contoh
docs/                       # Catatan desain/plan internal
PRD.md, DESIGN.md, schema.sql, AGENTS.md   # Dokumen acuan proyek
```

---

## Cara Menjalankan Secara Lokal

Prasyarat: PHP 8.3+, Composer, Node.js 18+, MySQL.

```bash
# 1. Clone repo
git clone https://github.com/FAYnim/Hamta-Healthy-Food-System.git
cd Hamta-Healthy-Food-System

# 2. Install dependency PHP & JS
composer install
npm install

# 3. Salin environment file
# Windows (PowerShell)
copy .env.example .env
# macOS/Linux
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Atur koneksi database di .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=...
#    DB_USERNAME=...
#    DB_PASSWORD=...
#    Buat database kosong dengan nama yang sama.

# 6. Migrasi + data awal (admin, cabang, bahan, komponen salad, produk)
php artisan migrate --seed

# 7. Symlink storage (wajib untuk foto produk & bukti bayar)
php artisan storage:link

# 8. Build asset frontend
npm run build

# 9. Jalankan aplikasi
php artisan serve
```

Buka `http://localhost:8000`.

### Mode development (hot reload)

```bash
composer dev
```

Script `composer dev` menjalankan tiga proses sekaligus: `php artisan serve`, `php artisan queue:listen`, dan `npm run dev` (didefinisikan di `composer.json` + `artisan dev`).

### Alternatif setup otomatis

```bash
composer setup
```

Menjalankan `composer install`, copy `.env`, `key:generate`, `migrate --force`, `npm install --ignore-scripts`, dan `npm run build`. Perintah ini **tidak** menjalankan seeder, jadi jalankan `php artisan db:seed` bila butuh data awal.

### Script yang tersedia

Dari `package.json`:

```bash
npm run dev      # vite dev server
npm run build    # vite build
```

Dari `composer.json`:

```bash
composer setup   # setup awal (lihat di atas)
composer dev     # server + queue + vite
composer test    # config:clear + php artisan test
```

### Akun admin hasil seeder

Dibuat oleh `database/seeders/AdminSeeder.php`:

- Username: `admin`
- Password: `hamta`

Ganti password ini sebelum dipakai di luar lingkungan lokal/demo.

---

## Environment Variables

Daftar variabel yang dibaca aplikasi (nama saja, tanpa nilai). Lihat `.env.example` untuk template.

**Wajib / inti**

- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER`, `SESSION_LIFETIME`
- `FILESYSTEM_DISK`
- `CACHE_STORE`, `QUEUE_CONNECTION`

**reCAPTCHA (login admin)**

- `RECAPTCHA_SITE_KEY`
- `RECAPTCHA_SECRET_KEY`

**Midtrans (QRIS)**

- `MIDTRANS_SERVER_KEY`
- `MIDTRANS_CLIENT_KEY`
- `MIDTRANS_IS_PRODUCTION`

**Opsional / bawaan Laravel**

- `LOG_CHANNEL`, `LOG_LEVEL`, `BCRYPT_ROUNDS`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`
- `VITE_APP_NAME`

Tanpa `MIDTRANS_SERVER_KEY`/`MIDTRANS_CLIENT_KEY`, checkout QRIS otomatis akan gagal dan aplikasi otomatis jatuh ke alur QRIS statis + upload bukti transfer.

---

## Status Pengembangan

Branch aktif: **`main`**. Seluruh branch fitur (`feat/customer`, `feat/admin`, `feat/admin-ux`, `feat/midtrans`, `feat/qris-expired-timer`, `feat/favicon`, `fix/checkout`) sudah di-merge ke `main` — tidak ada fitur yang menggantung di branch terpisah.

**Selesai**

- Alur customer end-to-end: katalog → kustom salad → checkout → pembayaran → status pesanan → rating.
- Panel admin lengkap: dashboard, kasir, pesanan, menu, promo, laporan, pengaturan.
- Integrasi QRIS Midtrans (charge + webhook verifikasi signature) dengan fallback QRIS statis.
- Diskon/promo otomatis dengan kuota dan stacking.

**Catatan / masih bisa dilanjutkan**

- Form checkout belum memakai reCAPTCHA meski konfigurasinya sudah tersedia.
- Verifikasi pembayaran QRIS statis masih manual oleh admin (sesuai desain tahap ini).
- `TempReviewProbeTest.php` di `tests/Feature` tampak seperti test sementara untuk verifikasi review, bukan test fitur permanen.

### Menjalankan test

```bash
composer test
# atau
php artisan test
# test tertentu
php artisan test --filter=AdminAuthTest
```

Test yang tersedia di `tests/Feature`: `AdminAuthTest`, `AutomatedBlackBoxTest`, `BlackboxFixesVerificationTest`, `KelolaMenuFilterTest`, `KelolaMenuStatusTest`, `MidtransIntegrationTest`, `PengaturanOngkirTest`, `PengaturanQrisStatisTest`, `PromoTerpakaiTest`, plus contoh di `tests/Unit`.

---

## Dokumen Terkait

- `PRD.md` — kebutuhan produk dan alur bisnis.
- `DESIGN.md` — design system (warna, komponen, layout).
- `schema.sql` — skema database final (acuan migrasi).
- `AGENTS.md` — konvensi kerja & aturan kontribusi.
- `REPORT.md` — laporan audit codebase.

Proyek PjBL kelas XI RPL 1, Kelompok 2, SMK Negeri 1 Surabaya.
