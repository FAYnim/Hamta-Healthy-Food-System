# Laporan Audit & Eksplorasi Codebase Hamta Salad (Laravel 13)

**Tanggal Audit:** 27 Oktober 2026  
**Auditor:** AI Codebase Auditor  
**Branch Aktif:** `feat/admin`  
**Commit Head:** `65c3fe5 feat: buat integrasi dashboard`  

---

## 1. Ringkasan Perubahan Sejak Audit Terakhir

Berikut adalah perbandingan status temuan audit terdahulu dengan kondisi riil codebase saat ini:

| Poin Temuan Lama | Status Saat Ini | Detail Verifikasi |
|---|---|---|
| `routes/web.php` hanya 8 closure route lama, tanpa named route & middleware | **BERUBAH SIGNIFIKAN** | `routes/web.php` kini memiliki 32 route terdaftar, mayoritas sudah memiliki named route dan berada di dalam grup `Route::middleware('admin.auth')`. Namun, **route `/admin/dashboard` masih tertinggal sebagai closure route**. |
| Semua 11 controller belum di-wire ke route | **SEBAGIAN BESAR TERHUBUNG (ADMIN)** | 6 dari 8 controller admin sudah di-wire penuh. 1 controller (`DashboardController`) belum dipanggil oleh routenya, 1 controller (`LaporanController`) sama sekali belum punya route. Seluruh 4 controller customer belum di-wire sama sekali. |
| Hanya 8 file Blade dan 100% dummy data hardcoded | **SUDAH DINAMIS (ADMIN)** | Seluruh file Blade admin (`login`, `dashboard`, `kelola_menu`, `daftar_pesanan`, `kasir`, `promo`, `pengaturan`, serta partial `pesanan_detail` dan component `sidebar`) telah dinamis menerima data dari database/controller. |
| Belum ada halaman customer-facing | **TETAP (BELUM ADA)** | Hanya terdapat `welcome.blade.php` (masih template skeleton default bawaan Laravel). Belum ada landing page, katalog, produk-detail, kreasikan-salad, checkout, status-pesanan, maupun rating. |
| Mismatch nama view controller vs file Blade | **SUDAH DIPERBAIKI** | `MenuController::index()` kini memanggil `view('admin.kelola_menu')`, cocok dengan nama file `resources/views/admin/kelola_menu.blade.php`. |
| `admin/login.blade.php` form dekoi | **SUDAH AKTIF & FUNGSIONAL** | Form login admin sudah dilengkapi `@csrf`, `method="POST"`, `action="{{ route('admin.login') }}"`, input `name="username"`, `name="password"`, dan feedback flash error/success. |
| Tabel produk dan promo kosong (belum ada seeder) | **TERISI SECARA MANUAL** | Tabel `produk` berisi 1 data dan `promo` berisi 1 data (hasil input manual melalui admin panel). File seeder resmi `ProdukSeeder` dan `PromoSeeder` **belum dibuat**. |
| Bug `$fillable` model Cabang | **SUDAH DIPERBAIKI** | Model `Cabang` sudah menggunakan `layani_pickup` dan `layani_dinein` secara konsisten. |

---

## 2. Status Git & Riwayat Commit

- **Branch aktif:** `feat/admin` (sinkron dengan `origin/feat/admin`)
- **Total commit di branch `feat/admin` (dibandingkan `main`):** 18 commit
- **Ringkasan 10 commit terakhir:**
  1. `65c3fe5` - *feat: buat integrasi dashboard* (menghubungkan statistik, chart, notifikasi)
  2. `de282d6` - *feat: buat fitur pengaturan* (identitas toko, logo upload, jam cabang, kontak)
  3. `9808b66` - *feat: buat fitur Promo* (CRUD promo, toggle status, validasi batas penggunaan)
  4. `52a27e6` - *feat: buat fitur Daftar Pesanan* (tabel pesanan, AJAX drawer detail, ubah status, konfirmasi ongkir)
  5. `e441ce0` - *feat: buat fitur kasir lengkap* (POS interaktif, custom salad builder modal, simpan transaksi kasir)
  6. `b5e47e5` - *feat: finishing fitur kelola menu* (filtering, search, modal tambah/edit menu, toggle status)
  7. `70c1612` - *feat: buat produk model, migration, controller, dan admin view*
  8. `19c4890` - *feat: pindah sidebar jadi components* (`<x-admin.sidebar />`)
  9. `47808ad` - *feat: buat middleware agar admin tidak bisa diakses tanpa login*
  10. `6a3606b` - *fix: authcontroller*

---

## 3. Audit Routes & Middleware

### A. Middleware & Keamanan Admin
- **Pendaftaran:** `bootstrap/app.php` telah mendefinisikan alias middleware:
  ```php
  $middleware->alias([
      'admin.auth' => \App\Http\Middleware\AdminAuthenticate::class,
  ]);
  ```
- **Logika Middleware:** `app/Http/Middleware/AdminAuthenticate.php` memeriksa keberadaan session `admin_id`. Jika tidak ada, pengguna di-redirect ke `route('admin.login')` dengan pesan flash error.
- **AuthController:** Menggunakan validasi `username` dan `password`, pencarian admin via Eloquent `Admin::where(...)`, dan verifikasi aman dengan `Hash::check()`. Session diregenerasi saat login (`admin_id`, `admin_nama`) dan di-invalidate serta di-regenerate token saat logout.
- **Catatan reCAPTCHA:** Kotak reCAPTCHA di halaman login saat ini masih berupa mockup visual SVG/HTML, belum terintegrasi dengan Google reCAPTCHA API script.

### B. Daftar Route Terdaftar (Routes vs Controllers)

```text
+--------+---------------------------------------+----------------------------------+-----------------------------------------------+----------------+
| Method | URI                                   | Name                             | Action / Handler                              | Middleware     |
+--------+---------------------------------------+----------------------------------+-----------------------------------------------+----------------+
| GET    | /                                     | -                                | Closure (welcome.blade.php)                   | web            |
| GET    | admin/login                           | admin.login                      | Admin\AuthController@showLoginForm            | web            |
| POST   | admin/login                           | -                                | Admin\AuthController@login                    | web            |
| POST   | admin/logout                          | admin.logout                     | Admin\AuthController@logout                   | web            |
| GET    | admin/dashboard                       | admin.dashboard                  | Closure (return view('admin.dashboard')) [!]  | web, admin.auth|
| GET    | admin/kasir                           | admin.kasir                      | Admin\KasirController@index                   | web, admin.auth|
| POST   | admin/kasir                           | admin.kasir.store                | Admin\KasirController@store                   | web, admin.auth|
| GET    | admin/kelola-menu                     | admin.kelola_menu                | Admin\MenuController@index                    | web, admin.auth|
| POST   | admin/kelola-menu                     | admin.kelola_menu.store          | Admin\MenuController@store                    | web, admin.auth|
| PATCH  | admin/kelola-menu/{id}                | admin.kelola_menu.update         | Admin\MenuController@update                   | web, admin.auth|
| DELETE | admin/kelola-menu/{id}                | admin.kelola_menu.destroy        | Admin\MenuController@destroy                  | web, admin.auth|
| PATCH  | admin/kelola-menu/{id}/status         | admin.kelola_menu.status         | Admin\MenuController@toggleStatus             | web, admin.auth|
| GET    | admin/daftar-pesanan                  | admin.daftar_pesanan             | Admin\PesananController@index                 | web, admin.auth|
| GET    | admin/daftar-pesanan/{id}             | admin.daftar_pesanan.show        | Admin\PesananController@show                  | web, admin.auth|
| PATCH  | admin/daftar-pesanan/{id}/status      | admin.daftar_pesanan.status      | Admin\PesananController@updateStatus          | web, admin.auth|
| PATCH  | admin/daftar-pesanan/{id}/ongkir      | admin.daftar_pesanan.ongkir      | Admin\PesananController@konfirmasiOngkir      | web, admin.auth|
| PATCH  | admin/daftar-pesanan/item/{id}/batal  | admin.daftar_pesanan.item.batal  | Admin\PesananController@batalkanItem          | web, admin.auth|
| GET    | admin/promo                           | admin.promo.index                | Admin\PromoController@index                   | web, admin.auth|
| POST   | admin/promo                           | admin.promo.store                | Admin\PromoController@store                   | web, admin.auth|
| PUT/PA | admin/promo/{id}                      | admin.promo.update               | Admin\PromoController@update                  | web, admin.auth|
| DELETE | admin/promo/{id}                      | admin.promo.destroy              | Admin\PromoController@destroy                 | web, admin.auth|
| PATCH  | admin/promo/{id}/status               | admin.promo.status               | Admin\PromoController@toggleStatus            | web, admin.auth|
| GET    | admin/pengaturan                      | admin.pengaturan                 | Admin\PengaturanController@index              | web, admin.auth|
| POST   | admin/pengaturan/identitas            | admin.pengaturan.identitas       | Admin\PengaturanController@updateIdentitas    | web, admin.auth|
| POST   | admin/pengaturan/kontak               | admin.pengaturan.kontak          | Admin\PengaturanController@updateKontak       | web, admin.auth|
| POST   | admin/pengaturan/cabang               | admin.pengaturan.cabang.store    | Admin\PengaturanController@storeCabang        | web, admin.auth|
| PATCH  | admin/pengaturan/cabang/{cabang}      | admin.pengaturan.cabang.update   | Admin\PengaturanController@updateCabang       | web, admin.auth|
| DELETE | admin/pengaturan/cabang/{cabang}      | admin.pengaturan.cabang.destroy  | Admin\PengaturanController@destroyCabang      | web, admin.auth|
+--------+---------------------------------------+----------------------------------+-----------------------------------------------+----------------+
```

---

## 4. Status Integrasi Controller (12 Controller)

Terdapat 12 controller di codebase (8 di namespace `App\Http\Controllers\Admin` dan 4 di `App\Http\Controllers`):

| # | Controller | Status Wiring | Keterangan & Analisis |
|---|---|---|---|
| 1 | `Admin\AuthController` | **WIRED** | Terhubung penuh (`showLoginForm`, `login`, `logout`). Form dan auth session bekerja aktif. |
| 2 | `Admin\DashboardController` | **NOT WIRED (CRITICAL)** | Method `index()` sudah lengkap meng-query data grafik, pesanan, omset, dan sold-out produk. Namun di `routes/web.php` line 23 masih berupa Closure `return view('admin.dashboard')`. **Halaman ini akan crash dengan error Undefined Variable jika diakses langsung.** |
| 3 | `Admin\KasirController` | **WIRED** | Terhubung penuh (`index`, `store`). Query master produk, komponen, promo, cabang, dan simpan transaksi POS terverifikasi otomatis via `DB::transaction`. |
| 4 | `Admin\MenuController` | **PARTIALLY WIRED** | `index`, `store`, `update`, `destroy`, `toggleStatus` sudah di-wire. Method `create()` dan `edit()` tidak di-wire karena UI menggunakan modal popup di dalam view index. |
| 5 | `Admin\PesananController` | **WIRED** | Terhubung penuh (`index`, `show`, `updateStatus`, `konfirmasiOngkir`, `batalkanItem`). Menggunakan eager loading `Pesanan::with(...)` untuk mencegah N+1. |
| 6 | `Admin\PromoController` | **WIRED** | Terhubung penuh (`index`, `store`, `update`, `destroy`, `toggleStatus`). Dilengkapi proteksi integritas FK: promo yang sudah dipakai pesanan tidak bisa dihapus, hanya dinonaktifkan. |
| 7 | `Admin\PengaturanController` | **WIRED** | Terhubung penuh (`index`, `updateIdentitas`, `updateKontak`, `storeCabang`, `updateCabang`, `destroyCabang`). Menggunakan model baru `Pengaturan` dan tabel `pengaturan`. |
| 8 | `Admin\LaporanController` | **NOT WIRED** | Method `index(Request $request)` sudah siap mengagregasi penjualan per item dengan rentang tanggal. Belum ada route di `routes/web.php` dan belum ada view `admin/laporan.blade.php`. |
| 9 | `ProdukController` (Customer) | **NOT WIRED** | Method `index()` dan `show($id)` sudah ditulis, tetapi belum dipasang di `routes/web.php`. View target (`katalog`, `produk-detail`) belum dibuat. |
| 10 | `KomponenSaladController` (Customer) | **NOT WIRED** | Method `index()` sudah ditulis, belum ada route di `routes/web.php`. View target `kreasikan-salad` belum dibuat. |
| 11 | `PesananController` (Customer) | **NOT WIRED** | Method `store()` (guest checkout online + verifikasi harga master DB + PromoService) dan `show($kode_pesanan)` sudah ditulis, belum ada route di `routes/web.php`. View target `status-pesanan` belum dibuat. |
| 12 | `RatingController` (Customer) | **NOT WIRED** | Method `store()` sudah ditulis, belum ada route di `routes/web.php`. |

---

## 5. Status Blade Views

### A. Admin Views (`resources/views/admin/` & `components/admin/`)

| File View | Status | Keterangan |
|---|---|---|
| `admin/login.blade.php` | **DYNAMIC** | Sudah dinamis: action route, CSRF, flash error/success, old input. |
| `admin/dashboard.blade.php` | **DYNAMIC (BREAKING ROUTE)** | View sudah siap menerima 7 variabel (`$jumlah_pesanan_hari_ini`, `$pendapatan_hari_ini`, `$jumlah_menunggu_deposit`, `$jumlah_pesanan_selesai`, `$grafik_penjualan`, `$notifikasi_terbaru`, `$jumlah_produk_sold_out`). Namun karena route belum memanggil `DashboardController`, view akan error saat dibuka. |
| `admin/kelola_menu.blade.php` | **DYNAMIC** | Loop data `$produk`, filter kategori, search bar, pagination link, modal tambah/edit menu, toggle status button. |
| `admin/daftar_pesanan.blade.php` | **DYNAMIC** | Loop data `$pesanan`, badge status, filter tab, search bar, trigger drawer detail via data-attribute AJAX. |
| `admin/partials/pesanan_detail.blade.php`| **DYNAMIC** | Subview detail pesanan untuk drawer: data pelanggan, item pesanan, rincian komponen salad custom, ongkir, promo, dan tombol aksi status. |
| `admin/kasir.blade.php` | **DYNAMIC** | Grid produk aktif, filter kategori, builder kreasikan salad modal, live cart, cabang selector, payment radio. |
| `admin/pengaturan.blade.php` | **DYNAMIC** | Form logo toko, nomor WhatsApp, Instagram, list cabang dengan form create/edit/delete cabang. |
| `admin/promo.blade.php` | **DYNAMIC** | Tabel promo aktif/nonaktif, hitung sisa kuota, badge tipe diskon, modal tambah/edit promo. |
| `components/admin/sidebar.blade.php` | **DYNAMIC** | Komponen sidebar admin reusable, mendeteksi route aktif (`request()->routeIs()`), terpasang di semua halaman admin. Menu Laporan belum dimasukkan ke sidebar. |
| `admin/laporan.blade.php` | **MISSING** | Belum ada file view untuk laporan penjualan item per periode. |
| `admin/menu/create.blade.php` & `edit.blade.php` | **MISSING (OBSOLETE)** | Dirujuk oleh method `MenuController::create()` dan `edit()`, tetapi tidak dibutuhkan karena UI admin Hamta memakai modal terpadu di `kelola_menu.blade.php`. |

### B. Customer-Facing Views (`resources/views/`)

| Halaman Sesuai PRD | File Target | Status | Kondisi Saat Ini |
|---|---|---|---|
| Landing Page (Beranda) | `welcome.blade.php` atau `landing.blade.php` | **MISSING / SKELETON** | File `welcome.blade.php` masih 100% template bawaan Laravel (Laravel logo, Documentation links). Belum ada hero section, katalog singkat, benefit, maupun CTA Hamta. |
| Katalog Produk | `katalog.blade.php` | **MISSING** | Belum ada file view. |
| Detail Produk | `produk-detail.blade.php` | **MISSING** | Belum ada file view. |
| Kreasikan Salad Sendiri | `kreasikan-salad.blade.php` | **MISSING** | Belum ada file view. |
| Keranjang & Checkout | `checkout.blade.php` | **MISSING** | Belum ada file view. |
| Status Pesanan & Pembayaran QRIS | `status-pesanan.blade.php` | **MISSING** | Belum ada file view. |
| Form Rating & Ulasan | Bagian dari status pesanan atau modal | **MISSING** | Belum ada file view. |

---

## 6. Audit Integrasi Frontend (CSS & JavaScript)

### A. JavaScript (`resources/js/app.js`)
- Berisi **449 baris kode JavaScript modular**:
  1. **Integrasi Chart.js:** Menggambar grafik penjualan 7 hari terakhir pada dashboard admin (`#dashboard-sales-chart`), membaca label dan value dari `dataset`, diformat ke Rupiah.
  2. **Kasir POS Client Engine:** Sistem interaktif keranjang belanja offline (tambah item reguler, kalkulasi stepper kuantitas, custom salad builder modal dengan kalkulasi harga dinamis salad dasar + sayur/protein, pencarian live produk, filter kategori, format Rupiah live, auto-generate hidden inputs `cart[i][...]`, serta parsing kembali old inputs jika validasi server-side gagal).

### B. Styling & Design Tokens (`resources/css/app.css`)
- **Tailwind CSS v4:** `@import 'tailwindcss';` sudah aktif via Vite plugin.
- **Inkonsistensi Color Palette:**
  - Token warna resmi Hamta (Green `#65BD5E`, Dark Green `#0B412B`, Cream `#F7F2E7`, Black `#010101`, Grey `#8A8A8A`, Red `#E05C5C`) **BELUM didefinisikan sebagai `@theme` tokens** di `resources/css/app.css`.
  - Akibatnya, seluruh Blade views saat ini menuliskan arbitrary values secara hardcoded berulang-ulang, misalnya: `bg-[#0B412B]`, `bg-[#F7F2E7]`, `bg-[#65BD5E]`, `text-[#010101]`, `text-[#8A8A8A]`.
- **Inkonsistensi Tipografi/Font:**
  - `resources/css/app.css` menetapkan: `--font-sans: 'Instrument Sans' ...`
  - Seluruh file Blade admin mengimpor Google Fonts: `<link href="...Plus+Jakarta+Sans...">` dan memaksanya melalui tag inline: `<style> body { font-family: 'Plus Jakarta Sans', ... } </style>`.

---

## 7. Audit Data & Seeder

Pemeriksaan database MySQL menunjukkan data berikut:

| Tabel | Jumlah Baris Aktual | Keterangan Sumber Data |
|---|---|---|
| `admin` | 1 baris | Di-seed oleh `AdminSeeder.php` (`username: admin`, nama: `Kak Yunita`) |
| `cabang` | 2 baris | Di-seed oleh `CabangSeeder.php` (Cabang Lidah Kulon & MERR) |
| `bahan` | 11 baris | Di-seed oleh `BahanSeeder.php` (Bahan-bahan salad) |
| `komponen_salad` | 11 baris | Di-seed oleh `KomponenSaladSeeder.php` (Sayuran & Protein) |
| `produk` | 1 baris | Data test manual dari panel admin ("Salad Sayur", Rp 15.000). **Belum ada `ProdukSeeder.php`**. |
| `promo` | 1 baris | Data test manual dari panel admin ("Diskon akhir pekan", potongan Rp 1.000). **Belum ada `PromoSeeder.php`**. |
| `pesanan` | 7 baris | Data pesanan test hasil uji coba fitur Kasir POS. |
| `pengaturan` | 0 baris | Dibuat on-demand lewat `Pengaturan::firstOrCreate()` saat halaman pengaturan dibuka. |

**DatabaseSeeder Status:**
`database/seeders/DatabaseSeeder.php` saat ini hanya mendaftarkan:
`AdminSeeder::class`, `CabangSeeder::class`, `BahanSeeder::class`, dan `KomponenSaladSeeder::class`.

---

## 8. Log Error (`storage/logs/laravel.log`)

- **Riwayat Syntax Error:** Terdapat log error parsing lama saat awal pengembangan routes pada 20 Oktober 2026.
- **Error `$pembayaran` di Daftar Pesanan:** Log mencatat error `Undefined variable $pembayaran` pada 25 Oktober 2026 di `admin/daftar_pesanan.blade.php`. Kode terkini telah diperiksa dan variabel tersebut **sudah dihapus/dibersihkan** di commit `52a27e6`.
- **Tidak ada error database deadlock atau unhandled crash baru** di sesi pengujian terakhir.

---

## 9. Daftar Bug & Inkonsistensi Baru yang Ditemukan

1. **Bug Fatal di Dashboard Route:**  
   Route `GET /admin/dashboard` di `routes/web.php` mengembalikan `view('admin.dashboard')` secara langsung via closure. Sementara view `admin/dashboard.blade.php` membutuhkan 7 variabel yang dipersiapkan oleh `DashboardController::index()`. Mengakses dashboard admin saat ini dipastikan melempar `ErrorException: Undefined variable $jumlah_produk_sold_out`.
2. **Controller `LaporanController` Terisolasi:**  
   Controller sudah memiliki logika kalkulasi penjualan per item yang rapi, namun tidak memiliki route di `routes/web.php`, tidak ada menu navigasi di `components/admin/sidebar.blade.php`, dan file view `admin/laporan.blade.php` belum dibuat.
3. **Konfigurasi Font Ganda:**  
   `app.css` mendefinisikan Instrument Sans, sedangkan setiap view blade memuat Plus Jakarta Sans lewat tag `<link>` eksternal dan inline `<style>`.
4. **Token Warna Belum Dideklarasikan di Tailwind v4:**  
   Palet warna utama Hamta belum dimasukkan ke `@theme` di `app.css`, mengakibatkan inkonsistensi maintainability karena penggunaan inline arbitrary hex values (`[#0B412B]`, dsb.) di setiap file template.
5. **Ketiadaan Route & View Customer-Facing:**  
   Frontend customer-facing saat ini masih kosong 0% (selain `welcome.blade.php` mentah), meskipun 4 controller customer-facing sudah selesai ditulis.

---

## 10. Gap Sebelum Mulai Development Halaman Customer-Facing

Sebelum tim melangkah membuat tampilan customer-facing (landing page, katalog, checkout, dll.), gap-gap teknis fondasi berikut harus diselesaikan terlebih dahulu:

1. **Fixing Dashboard Wiring:** Ubah Closure route `admin/dashboard` di `routes/web.php` menjadi `[DashboardController::class, 'index']`.
2. **Wiring Routes Customer:** Daftarkan route katalog, kreasikan-salad, checkout, status-pesanan, dan rating di `routes/web.php` mengarah ke 4 controller yang sudah tersedia.
3. **Standarisasi Theme Tailwind CSS v4 & Font:**
   - Tambahkan token `@theme` palet Hamta (`hamta-green`, `hamta-dark`, `hamta-cream`, `hamta-black`, `hamta-gray`, `hamta-red`) di `resources/css/app.css`.
   - Tetapkan Plus Jakarta Sans secara sentral di `@theme` sehingga view Blade tidak perlu mengulang tag `<style>` lokal.
4. **Layout Customer Reusable:**
   - Buat master layout untuk customer (misal `resources/views/layouts/app.blade.php` atau component `<x-layout.customer>`), yang memuat navbar customer (logo, link katalog, kreasikan salad, keranjang melayang) dan footer (kontak WA, cabang, medsos dari model `Pengaturan`).
5. **Seeder Produk & Promo:**
   - Buat `ProdukSeeder` (isi menu salad, sandwich, juice Hamta yang representatif) dan `PromoSeeder` agar saat membangun halaman katalog & checkout tidak mengandalkan database kosong.

---

## 11. Rekomendasi Urutan Langkah Berikutnya

```text
Langkah 1: Perbaikan Bug Admin (Quick Wins)
 ├── 1.1 Wire DashboardController@index ke route /admin/dashboard di routes/web.php
 └── 1.2 Wire LaporanController@index dan buat view admin/laporan.blade.php (opsional sebelum customer)

Langkah 2: Standardisasi Design System & Layout
 ├── 2.1 Daftarkan color tokens & font Plus Jakarta Sans di resources/css/app.css (@theme)
 └── 2.2 Buat customer layout component (<x-customer.layout>) lengkap dengan navbar & footer dinamis

Langkah 3: Pembuatan Seeder Katalog & Promo
 └── 3.1 Buat ProdukSeeder & PromoSeeder, daftarkan di DatabaseSeeder.php

Langkah 4: Wire Route Customer & Pembuatan Blade Views Customer
 ├── 4.1 Wire routes: /, /katalog, /produk/{id}, /kreasikan-salad, /checkout, /pesanan/{kode_pesanan}, /pesanan/{kode_pesanan}/rating
 ├── 4.2 Landing Page (resources/views/landing.blade.php atau welcome.blade.php)
 ├── 4.3 Katalog Menu (resources/views/katalog.blade.php & produk-detail.blade.php)
 ├── 4.4 Halaman Kreasikan Salad Sendiri (resources/views/kreasikan-salad.blade.php)
 ├── 4.5 Keranjang & Checkout Mandiri QRIS (resources/views/checkout.blade.php)
 └── 4.6 Status Pesanan & Pembayaran QRIS + Form Rating (resources/views/status-pesanan.blade.php)
```
