# AGENTS.md — Hamta Healthy Food (Hamtash Salad)

## Setup Awal (Laravel Application)

Repositori ini adalah aplikasi Laravel. Selesaikan setup berikut sebelum mengerjakan request apa pun.

### Prerequisites

Pastikan PHP dan Composer tersedia:

```sh
php -v
composer -V
```

Jika salah satu belum tersedia, deteksi OS yang dipakai lalu install dengan perintah yang sesuai:

**macOS:**
```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

**Windows PowerShell:**
```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

**Linux:**
```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

Setelah instalasi, minta user restart terminal. Jika agent butuh shell yang sudah di-restart untuk melanjutkan, minta user membuka ulang terminal dan menjalankan ulang prompt awalnya.

### Agent Setup

Install Laravel Boost dari root aplikasi sebelum melakukan perubahan apa pun ke aplikasi:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost akan menggantikan instruksi bootstrap ini dengan guidelines yang disesuaikan untuk aplikasi. Setelah instalasi, baca ulang `AGENTS.md` dan lanjutkan dengan request user asli menggunakan guidelines yang sudah di-generate.

---

## Tentang Proyek
Website + sistem kasir (POS) all-in-one untuk UMKM Hamtash Salad (brand "Hamta Healthy Food").
Proyek Project Based Learning (PjBL) kelas XI RPL 1, Kelompok 2, SMK Negeri 1 Surabaya.
Owner/klien: Kak Yunita.

Dua masalah bisnis yang diselesaikan:
1. Pelanggan berulang kali tanya katalog/harga lewat WhatsApp → dijawab dengan katalog digital transparan di website.
2. Margin tertekan komisi platform pihak ketiga (GoFood/GrabFood/ShopeeFood) karena belum punya kurir internal → dijawab dengan checkout mandiri via QRIS langsung di website (bukan redirect WhatsApp).

Dua sisi aplikasi:
- **Customer-facing**: katalog menu, fitur "Kreasikan Salad Sendiri", keranjang & checkout mandiri (guest checkout, tanpa login), pilih pickup/delivery & cabang, lihat status pesanan, beri rating.
- **Admin panel**: Dashboard, Kelola Menu (termasuk komponen custom salad & bahan), Kasir (input transaksi offline), Daftar Pesanan/Kelola Pesanan (online + offline), Pengaturan Promo/Diskon, Laporan, Pengaturan Toko.

## Tech Stack
- **Framework**: Laravel 13 + Blade (full-stack monolith, bukan API-only)
- **Styling**: Tailwind CSS via Vite
- **Database**: MySQL, pakai Eloquent ORM biasa
- **Auth**: manual (bukan Breeze/Jetstream), hanya untuk Admin — customer selalu guest, tidak ada sistem login customer
- **Storage**: local disk Laravel (`storage/app/public` + symlink), belum ada cloud storage
- **Icon**: Lucide atau Heroicons (SVG)
- **Payment**: QRIS statis dengan verifikasi bukti bayar manual oleh admin (TAHAP SEKARANG). Midtrans Snap direncanakan sebagai fitur akhir — JANGAN implementasi webhook Midtrans dulu sebelum diminta eksplisit.
- **Captcha**: Google reCAPTCHA v2, di form checkout & opsional login admin
- **Environment**: PHP 8.5.8, Node 18+

## Konvensi Project
- Struktur folder: ikuti default Laravel (`app/Models`, `app/Http/Controllers`, `resources/views` pakai Blade, `routes/web.php`)
- Views: Blade components untuk elemen reusable (product card, admin sidebar, stat card, dll) sesuai DESIGN.md
- Penamaan domain bisnis: bahasa Indonesia untuk nama tabel/field/menu di level domain bisnis (misal `pesanan`, `komponen_salad`) boleh dipakai agar konsisten dengan dokumen (PRD/ERD)
- **Penamaan file & variable**: untuk hal-hal yang bebas ditentukan (nama file Blade view/partial, nama variabel dalam controller/view, nama route, dsb.) pakai **snake_case** (misal `produk_salad.blade.php`, `$daftar_promo`, route name `kelola_menu`). Untuk yang sudah dipaksa konvensi Laravel (nama Model = PascalCase/singular, nama Controller = PascalCase + suffix `Controller`, nama Migration = default Laravel generator), tetap ikuti konvensi Laravel tersebut — snake_case hanya berlaku di luar itu
- Setiap fitur baru dikerjakan di branch sendiri: `fitur/nama-fitur`

## Referensi Dokumen Lain
- `PRD.md` — product requirement document lengkap
- `schema.sql` — skema database final (hasil translate dari ERD)
- `DESIGN.md` — design system (warna, komponen, layout)
- Diagram UML (use case, sequence) dan ERD — acuan alur bisnis & relasi data

## Aturan Kerja AI Agent
- Jangan generate fitur di luar scope yang disepakati di PRD tanpa konfirmasi ke tim dulu
- Jangan implementasi Midtrans/payment gateway otomatis — masih manual QRIS statis untuk tahap ini
- Ikuti palet warna & role warna dari DESIGN.md untuk semua UI yang dibuat
- Customer flow = guest checkout only, jangan tambahkan sistem registrasi/login customer
- Setiap perubahan schema database harus konsisten dengan `schema.sql`, bukan dibuat ad-hoc di migration tanpa mengacu ke situ