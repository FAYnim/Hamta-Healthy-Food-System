# Laporan Audit & Eksplorasi Codebase Laravel — Hamta Healthy Food

**Tanggal Audit**: 8 November 2026  
**Repositori**: Hamta Healthy Food (Hamtash Salad) — Monolith Laravel 13 + Blade + Tailwind CSS  
**Tujuan**: Pemetaan menyeluruh status Git, Skema & Migrasi Database, Alur Pembayaran, Routes, Views, Konfigurasi, dan Kesiapan Integrasi Payment Gateway (Midtrans).

---

## 1. STATUS GIT

### A. Branch Aktif & Daftar Branch
- **Branch Aktif Saat Ini**: `main`
- **Status Branch**: Lebih maju 2 commit dari remote (`ahead of 'origin/main' by 2 commits`). Working tree bersih (*nothing to commit, working tree clean*).
- **Daftar Branch Lokal**:
  - `main` (aktif)
  - `feat/admin`
  - `feat/customer`
  - `+ worktree-agent-a7eb11e5606eb1fe9` (linked worktree)
- **Daftar Branch Remote (`origin`)**:
  - `origin/main`
  - `origin/feat/admin`
  - `origin/feat/customer`

### B. Jumlah Commit & Riwayat 10 Commit Terakhir
- **Total Commit di Branch `main`**: 41 commit
- **Commit yang Belum Ter-push ke `origin/main`**: 2 commit (`8828f19`, `47e7dfb`)
- **10 Commit Terakhir**:
  1. `8828f19` (2026-11-08) — *feat: add customer story, status pesanan, and header layout views*
  2. `47e7dfb` (2026-11-08) — *feat: add customer footer component and welcome landing page view*
  3. `8fd4388` (2026-11-08) — *feat: add services configuration and update recaptcha env placeholders*
  4. `63a55d5` (2026-11-08) — *Merge pull request #5 from FAYnim/feat/customer*
  5. `ec997ab` (2026-11-08) — *menambahkan fitur captcha*
  6. `8ec83e3` (2026-11-06) — *Merge pull request #3 from FAYnim/feat/customer*
  7. `7a0daaf` (2026-11-04) — *update fitur customer*
  8. `a77de2b` (2026-11-01) — *update landing page dan halaman pembayaran*
  9. `9130e72` (2026-10-30) — *feat: checkout*
  10. `062850b` (2026-10-29) — *feat: checkout*

---

## 2. SCHEMA & MIGRATIONS

### A. Tabel yang Sudah Dimigrasikan vs `schema.sql`
Semua tabel bisnis yang tertera pada `schema.sql` telah berhasil dimigrasikan ke database MySQL (`hamta_db`) dengan status `Ran`.

| Nama Tabel di DB | File Migrasi Terkait | Batch | Status vs `schema.sql` |
|---|---|:---:|---|
| `cabang` | `2026_10_21_020130_create_cabang_table.php` | 1 | **Sesuai** (termasuk `jam_buka_weekend`, `jam_tutup_weekend`, `layani_pickup`, `layani_dinein`) |
| `admin` | `2026_10_21_020133_create_admin_table.php` | 1 | **Sesuai** |
| `bahan` | `2026_10_21_020135_create_bahan_table.php` | 1 | **Sesuai** |
| `produk` | `2026_10_21_020138_create_produk_table.php` | 1 | **Sesuai** (`harga_offline`, `harga_online`, `bisa_dikustom`, dsb.) |
| `produk_bahan` | `2026_10_21_020140_create_produk_bahan_table.php` | 1 | **Sesuai** (junction komposisi/alergen) |
| `komponen_salad` | `2026_10_21_020143_create_komponen_salad_table.php` | 1 | **Sesuai** (`sayuran`, `protein`, `harga_tambahan`) |
| `komponen_bahan` | `2026_10_21_020145_create_komponen_bahan_table.php` | 1 | **Sesuai** |
| `promo` | `2026_10_21_020147_create_promo_table.php` | 1 | **Sesuai** (`nominal`, `persen`, `gratis_ongkir`) |
| `pesanan` | `2026_10_21_020149_create_pesanan_table.php` | 1 | **Sesuai** |
| `detail_pesanan` | `2026_10_21_020151_create_detail_pesanan_table.php` | 1 | **Sesuai** |
| `detail_pesanan_komponen` | `2026_10_21_020154_create_detail_pesanan_komponen_table.php` | 1 | **Sesuai** |
| `pesanan_promo` | `2026_10_21_020156_create_pesanan_promo_table.php` | 1 | **Sesuai** (junction stacking promo) |
| `rating` | `2026_10_21_020158_create_rating_table.php` | 1 | **Sesuai** |
| `pengaturan` | `2026_10_24_000000_create_pengaturan_and_add_jam_weekend_table.php` | 2 | **Sesuai** (dimigrasikan di batch 2) |

*Tabel sistem bawaan Laravel yang juga ada di DB*: `users`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations`, `password_reset_tokens`, `sessions`.

### B. Definisi Persis Kolom Terkait Pembayaran di Tabel `pesanan`
Berdasarkan hasil `DESCRIBE pesanan;` langsung dari MySQL:

```sql
+-------------------+---------------------------------------------------------+------+-----+---------------------+----------------+
| Field             | Type                                                    | Null | Key | Default             | Extra          |
+-------------------+---------------------------------------------------------+------+-----+---------------------+----------------+
| metode_bayar      | enum('qris','tunai')                                    | NO   |     | qris                |                |
| bukti_bayar       | varchar(255)                                            | YES  |     | NULL                |                |
| status_pembayaran | enum('menunggu_verifikasi','terverifikasi','ditolak')   | NO   |     | menunggu_verifikasi |                |
| ongkir            | decimal(10,2)                                           | YES  |     | NULL                |                |
| status_ongkir     | enum('tidak_perlu','menunggu_konfirmasi','terkonfirmasi')| NO   |     | tidak_perlu         |                |
| subtotal          | decimal(10,2)                                           | NO   |     | NULL                |                |
| total_diskon      | decimal(10,2)                                           | NO   |     | 0.00                |                |
| total_bayar       | decimal(10,2)                                           | NO   |     | NULL                |                |
| status_pesanan    | enum('diproses','siap','selesai','batal')               | NO   |     | diproses            |                |
+-------------------+---------------------------------------------------------+------+-----+---------------------+----------------+
```

**Temuan Kunci Skema Database**:
1. `metode_bayar` hanya mengizinkan nilai `'qris'` dan `'tunai'`. Belum ada opsi `'midtrans'` atau payment gateway lain.
2. `bukti_bayar` berupa `varchar(255)` yang menyimpan path file upload ke local storage Laravel (`storage/app/public/bukti_bayar`).
3. `status_pembayaran` hanya memiliki 3 state: `'menunggu_verifikasi'`, `'terverifikasi'`, `'ditolak'`. Belum mencakup status standar gateway seperti `pending`, `settlement`, `expire`, dsb.
4. Belum ada kolom untuk token transaksi gateway, seperti `snap_token` atau `midtrans_transaction_id`.

---

## 3. PAYMENT FLOW SAAT INI

### A. Controller yang Aktif vs Controller Legacy
- **Controller Aktif**: `App\Http\Controllers\Customer\CheckoutController` (dijalankan via route `POST /checkout`).
- **Controller Legacy/Tidak Terpakai**: `App\Http\Controllers\PesananController` (berada di namespace root `App\Http\Controllers`, berisi method `store()` dan `show()`, tetapi tidak didaftarkan di `routes/web.php`).

### B. Alur Lengkap Baris per Baris (`CheckoutController@store`)
1. **Decode Cart** (Baris 24–29):
   Menerima payload yang dikirim dari browser via `FormData` multipart. Jika `cart` bertipe string JSON, otomatis di-decode menjadi array PHP.
2. **Validasi Request** (Baris 32–54):
   - `nama_pelanggan`: `['required', 'string', 'max:100']`
   - `no_whatsapp`: `['required', 'string', 'max:20']`
   - `metode_ambil`: `['required', Rule::in(['pickup', 'delivery'])]`
   - `alamat_delivery`: `['required_if:metode_ambil,delivery', 'nullable', 'string', 'max:255']`
   - `metode_bayar`: `['required', Rule::in(['qris', 'tunai'])]`
   - `bukti_bayar`: `['required_if:metode_bayar,qris', 'nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120']` (wajib upload bukti bayar jika metode QRIS)
   - `cart`: `['required', 'array', 'min:1']` beserta validasi tipe item (`produk` atau `custom`), `id_produk`, `id_komponen`, dan `jumlah`.
3. **Penyimpanan Berkas Bukti Bayar** (Baris 56–59):
   Jika terdapat file `bukti_bayar`, disimpan ke disk publik: `$request->file('bukti_bayar')->store('bukti_bayar', 'public')`.
4. **Pemilihan Cabang Fallback** (Baris 63–69):
   Mengambil cabang pertama dari database (`Cabang::orderBy('id_cabang')->first()`) sebagai fallback sementara karena UI checkout belum memiliki dropdown cabang.
5. **Transaksi Database Atomik (`DB::transaction`)** (Baris 72–195):
   - **Anti-tamper Harga** (Baris 74–86): Mengambil seluruh `id_produk` dan `id_komponen` yang dipesan langsung dari tabel `produk` dan `komponen_salad` di database. Harga satuan diambil dari kolom `harga_online` master produk dan `harga_tambahan` komponen salad, bukan dari request klien.
   - **Kalkulasi Subtotal** (Baris 88–143):
     - Jika tipe `custom`: harga = `harga_online produk` + `sum(harga_tambahan komponen)`; nama item diset "Kreasikan Salad Sendiri" dan `id_produk` diset `NULL` sesuai konvensi skema `detail_pesanan`.
     - Jika tipe `produk`: harga = `harga_online produk`.
   - **Insert Header Pesanan** (Baris 146–166):
     - `kode_pesanan`: Di-generate via `generateKodePesanan()` dengan format `HMT-ymd-XXXXX`.
     - `jenis`: `'online'`.
     - `ongkir`: Jika `delivery` diset `null`; jika `pickup` diset `0`.
     - `status_ongkir`: Jika `delivery` diset `'menunggu_konfirmasi'`; jika `pickup` diset `'tidak_perlu'`.
     - `subtotal`: Subtotal produk.
     - `total_diskon`: Awal `0`.
     - `total_bayar`: Sementara diset sama dengan subtotal.
     - `metode_bayar`: `'qris'` atau `'tunai'`.
     - `bukti_bayar`: Path file bukti transfer.
     - `status_pembayaran`: `'menunggu_verifikasi'`.
     - `status_pesanan`: `'diproses'`.
   - **Insert Detail Pesanan & Relasi Komponen** (Baris 169–188):
     - Menyimpan baris `detail_pesanan`.
     - Melakukan attach ID komponen dan snapshot harga tambahan ke tabel pivot `detail_pesanan_komponen`.
   - **Penerapan Promo Otomatis & Stacking** (Baris 190–193):
     - Memanggil `app(PromoService::class)->terapkanKePesanan($pesanan, (int) $totalPorsi)`.
     - Service mencari promo aktif yang memenuhi syarat min porsi, mengurangi kuota pakai secara atomik (`pesanSlotKuota`), menyimpan ke `pesanan_promo`, lalu memperbarui header pesanan:
       `total_diskon` = total diskon promo  
       `total_bayar` = `subtotal - total_diskon + (ongkir ?? 0)`
6. **Return Response JSON** (Baris 197–211):
   Mengembalikan HTTP 201 dengan data pesanan dalam bentuk JSON untuk diproses oleh JavaScript pada frontend `checkout.blade.php`.

### C. Logic Terkait Ongkir / Delivery vs Pickup
1. **Saat Checkout Dibuat**:
   - Jika `metode_ambil === 'delivery'`, nilai `ongkir` disimpan `NULL` dan `status_ongkir = 'menunggu_konfirmasi'`.
   - Nilai `total_bayar` yang tersimpan di DB saat checkout hanyalah nilai produk (`subtotal - diskon`). Ongkir belum ikut terhitung.
2. **Kapan `total_bayar` Menjadi Final?**
   - Pesanan **Pickup**: `total_bayar` **sudah final** langsung saat checkout selesai, karena tidak ada biaya pengiriman (`ongkir = 0`, `status_ongkir = 'tidak_perlu'`).
   - Pesanan **Delivery**: `total_bayar` **belum final** saat checkout. Admin harus menghubungi pembeli via WhatsApp untuk menyepakati ongkir, lalu admin menginput ongkir melalui rute admin `PATCH /admin/daftar-pesanan/{id}/ongkir` (`Admin\PesananController@konfirmasiOngkir`).
   - Di method `konfirmasiOngkir`, kolom `ongkir` diisi nominal dan `status_ongkir` diubah ke `'terkonfirmasi'`. Kemudian dipanggil `PromoService::terapkanUlang()`, di mana promo bertipe `gratis_ongkir` (jika ada) baru dihitung nilainya, dan `total_bayar` diperbarui menjadi `subtotal - total_diskon + ongkir`. Di sinilah tagihan delivery baru menjadi final.
3. **Anomali Flow Pembayaran QRIS Saat Ini**:
   - Pada `checkout.blade.php`, saat memilih `delivery` dan `qris`, customer **diwajibkan mengunggah bukti bayar QRIS di muka** pada halaman checkout, padahal nominal ongkir belum dihitung oleh admin. Artinya customer saat ini hanya membayar subtotal produk, sementara biaya ongkir harus dibayar secara terpisah nantinya.

### D. Keberadaan Kode Midtrans / Gateway Sebelumnya
- Dilakukan pencarian menyeluruh dengan `git grep -i "midtrans"`:
  - **Hasil**: Sama sekali **TIDAK DITEMUKAN** implementasi kode Midtrans di seluruh controller, service, middleware, model, view, maupun asset JS.
  - Kata "Midtrans" hanya disebut sebagai rencana masa depan di dalam file dokumentasi (`AGENTS.md` dan `PRD.md`).

---

## 4. ROUTES

Daftar seluruh rute yang berhubungan dengan Checkout, Pesanan, dan Pembayaran:

### A. Customer-Facing Routes
| Method | URI | Route Name | Action / Handler | Tipe Handler |
|---|---|---|---|---|
| `GET` | `/checkout` | `customer.checkout` | `resources/views/customer/checkout.blade.php` | `Route::view` |
| `POST` | `/checkout` | `customer.checkout.store` | `App\Http\Controllers\Customer\CheckoutController@store` | **Controller** |
| `GET` | `/alamat` | `customer.alamat` | `resources/views/customer/alamat.blade.php` | `Route::view` |
| `GET` | `/pembayaran` | `customer.pembayaran` | `resources/views/customer/pembayaran.blade.php` | `Route::view` |
| `GET` | `/status-pesanan/{kode?}` | `customer.status_pesanan` | `App\Http\Controllers\Customer\StatusPesananController@show` | **Controller** |
| `GET` | `/api/pesanan/{kode}/status` | `customer.pesanan.api_status` | `App\Http\Controllers\Customer\StatusPesananController@cek` | **Controller** |

*Catatan*: Route `/pesanan/{kode}` **tidak terdaftar** di `routes/web.php`. Endpoint yang dipakai di aplikasi adalah `/status-pesanan/{kode?}`.

### B. Admin-Facing Routes (Protected by `admin.auth`)
| Method | URI | Route Name | Action / Handler | Tipe Handler |
|---|---|---|---|---|
| `GET` | `/admin/daftar-pesanan` | `admin.daftar_pesanan` | `App\Http\Controllers\Admin\PesananController@index` | **Controller** |
| `GET` | `/admin/daftar-pesanan/{id}` | `admin.daftar_pesanan.show` | `App\Http\Controllers\Admin\PesananController@show` | **Controller** |
| `PATCH` | `/admin/daftar-pesanan/{id}/status` | `admin.daftar_pesanan.status` | `App\Http\Controllers\Admin\PesananController@updateStatus` | **Controller** |
| `PATCH` | `/admin/daftar-pesanan/{id}/pembayaran` | `admin.daftar_pesanan.pembayaran` | `App\Http\Controllers\Admin\PesananController@updatePembayaran` | **Controller** |
| `PATCH` | `/admin/daftar-pesanan/{id}/ongkir` | `admin.daftar_pesanan.ongkir` | `App\Http\Controllers\Admin\PesananController@konfirmasiOngkir` | **Controller** |
| `PATCH` | `/admin/daftar-pesanan/item/{id}/batal` | `admin.daftar_pesanan.item.batal` | `App\Http\Controllers\Admin\PesananController@batalkanItem` | **Controller** |
| `GET` | `/admin/kasir` | `admin.kasir` | `App\Http\Controllers\Admin\KasirController@index` | **Controller** |
| `POST` | `/admin/kasir` | `admin.kasir.store` | `App\Http\Controllers\Admin\KasirController@store` | **Controller** |

---

## 5. VIEWS

### A. Blade Views yang Terlibat dalam Flow Checkout & Status Pesanan
1. **`resources/views/customer/checkout.blade.php`** (Ukuran: 65 KB, 1.059 baris):
   - **Status**: Sangat aktif dan lengkap.
   - **Fungsi**:
     - Membaca cart dari localStorage (`hamta_cart`).
     - Menampilkan formulir checkout: alamat pengiriman (dibaca dari `hamta_address`), opsi pickup vs delivery, radio opsi metode pembayaran (QRIS statis vs Tunai).
     - Jika memilih QRIS: menampilkan gambar QR code statis Hamta dan input unggah bukti pembayaran transfer.
     - Ringkasan subtotal & total harga.
     - Mengirim request POST via Fetch API ke route `customer.checkout.store`.
     - Memiliki state view switcher: Jika pesanan berhasil disimpan, menyembunyikan `#co-form-view` dan menampilkan `#co-success-view` yang memuat kode pesanan, rincian, tombol salin kode, tombol tautan WhatsApp, dan tombol navigasi ke halaman pelacak live.
2. **`resources/views/customer/alamat.blade.php`** (Ukuran: 20 KB):
   - **Status**: Aktif.
   - **Fungsi**: Antarmuka untuk menyimpan nama, nomor WhatsApp, alamat jalan, dan patokan ke dalam `localStorage` browser (`hamta_address`) yang otomatis di-inject ke form checkout.
3. **`resources/views/customer/pembayaran.blade.php`** (Ukuran: 15 KB):
   - **Status**: Halaman transisi / legacy mockup UI.
   - **Fungsi**: Berjudul "Konfirmasi Pesanan", berisi daftar item keranjang dan simulasi ongkir dummy. Tombol konfirmasi di bagian bawah halaman ini langsung melakukan redirect: `window.location.href = "{{ route('customer.checkout') }}"`.
4. **`resources/views/customer/status-pesanan.blade.php`** (Ukuran: 30 KB, 481 baris):
   - **Status**: **SUDAH ADA dan LENGKAP**.
   - **Fungsi**:
     - Menerima parameter kode pesanan via URL `/status-pesanan/{kode?}` atau form pencarian jika kode kosong.
     - Jika kode tidak ada / pesanan belum dicari: menampilkan card pencarian pesanan dengan input kode pesanan serta quick link ke pesanan terakhir di perangkat dari localStorage.
     - Jika pesanan ditemukan:
       - **Hero Live Banner**: Menampilkan kode pesanan, waktu transaksi, judul status besar.
       - **Visual Stepper (4 Langkah)**: `1. Diterima` -> `2. Diproses` -> `3. Siap` -> `4. Selesai`, dengan animasi progress bar dinamis sesuai kolom `status_pesanan`. Jika status `'batal'`, stepper digantikan badge peringatan pesanan dibatalkan.
       - **Badge Status Pembayaran**: Menampilkan status verifikasi QRIS (`QRIS — Menunggu Verifikasi Admin`, `QRIS — Terverifikasi (Lunas)`, `Pembayaran Ditolak`) atau `Bayar di Tempat (Tunai)`.
       - **Metode Pengambilan & Alamat**: Menampilkan rincian Delivery vs Pickup beserta alamat/cabang tujuan.
       - **Daftar Menu & Komponen**: Menampilkan item yang dipesan, harga satuan, status jika ada item yang dibatalkan sepihak karena stok habis, dan total akhir.
       - **Tombol WhatsApp Admin**: Tombol bantuan dengan pesan template otomatis yang memuat kode pesanan dan nama pelanggan.
       - **Live Realtime Polling**: Memiliki JavaScript polling otomatis yang menembak route API `customer.pesanan.api_status` (`/api/pesanan/{kode}/status`) setiap beberapa detik untuk memperbarui badge dan stepper tanpa reload browser.
5. **`resources/views/status-pesanan.blade.php` (di root views)**:
   - **Status**: **TIDAK ADA**.
   - File ini sempat dipanggil oleh controller lama `App\Http\Controllers\PesananController@show` (`return view('status-pesanan', ...)`), yang bila dipanggil akan error. View yang benar dan digunakan oleh route resmi berada di `customer.status-pesanan`.

---

## 6. KONFIGURASI

### A. Environment Files (`.env.example` & `.env`)
- Di `.env.example`: **Tidak ada** variabel terkait Midtrans (hanya ada konfigurasi umum Laravel dan reCAPTCHA: `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`).
- Tidak ada placeholder `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, atau `MIDTRANS_IS_PRODUCTION`.

### B. Konfigurasi Service (`config/services.php`)
- `config/services.php` saat ini hanya berisi:
  ```php
  'postmark' => [...],
  'resend' => [...],
  'ses' => [...],
  'slack' => [...],
  'recaptcha' => [
      'site_key' => env('RECAPTCHA_SITE_KEY'),
      'secret_key' => env('RECAPTCHA_SECRET_KEY'),
  ],
  ```
- Tidak ada blok konfigurasi `'midtrans' => [...]`.

### C. Dependencies (`composer.json`)
- Package `midtrans/midtrans-php` **BELUM TERPASANG** di `composer.json`.
- Package pihak ketiga yang terpasang saat ini hanyalah:
  - `laravel/framework`: `^13.17`
  - `laravel/tinker`: `^3.0`
  - `mallardduck/blade-lucide-icons`: `^2.0`
  - `laravel/boost`: `^2.10` (dev)

---

## 7. GAP & REKOMENDASI (TODO)

### A. Apa yang SUDAH ADA dan Siap Dipakai
1. Struktur relasi database transaksi yang solid (`pesanan`, `detail_pesanan`, `detail_pesanan_komponen`, `pesanan_promo`, `cabang`).
2. Logika validasi backend anti-manipulasi harga: harga produk dan komponen salad selalu dihitung ulang dari database master (`harga_online`).
3. Sistem promo otomatis dan stacking diskon melalui `PromoService` yang berjalan atomik di dalam `DB::transaction`.
4. Halaman pelacakan status pesanan modern (`customer.status-pesanan`) dengan dukungan visual stepper, badge pembayaran, dan endpoint polling realtime (`/api/pesanan/{kode}/status`).
5. Admin panel lengkap untuk manajemen pesanan: verifikasi pembayaran manual, pengubahan status pengerjaan, konfirmasi tarif ongkir, dan pembatalan item parsial.

### B. Apa yang BELUM ADA Sama Sekali
1. **Composer Package**: Belum ada library `midtrans/midtrans-php`.
2. **Kredensial & Konfigurasi**: Belum ada Server Key, Client Key, Merchant ID, dan flag Sandbox/Production di `.env.example` maupun `config/services.php`.
3. **Skema Kolom Database**:
   - Kolom `pesanan.metode_bayar` hanya enum `['qris', 'tunai']`, belum ada opsi pembayaran via payment gateway.
   - Kolom `pesanan.status_pembayaran` hanya enum `['menunggu_verifikasi', 'terverifikasi', 'ditolak']`, belum ada pemetaan status gateway (`pending`, `settlement`, `expire`, `cancel`, `deny`).
   - Belum ada kolom untuk menyimpan `snap_token` (VARCHAR 255 NULL) dan `midtrans_transaction_id` / `payment_type` pada tabel `pesanan`.
4. **Webhook Notification Endpoint**: Belum ada Controller dan Route untuk menangani HTTP POST notification callback dari server Midtrans.
5. **CSRF Exemption**: Route webhook Midtrans belum dikecualikan dari verifikasi CSRF di `bootstrap/app.php`.
6. **Frontend Integration**: Belum ada skrip Snap JS (`snap.js` atau `snap.pay()`) di view checkout.

### C. Potensi Konflik & Hal Penting yang Perlu Diputuskan

> [!WARNING]
> ### 1. Dilema Tarif Ongkir Delivery vs Nominal Tagihan Midtrans
> Ini adalah keputusan arsitektur bisnis paling kritikal:
> - Pada alur saat ini, tarif ongkir pesanan **Delivery** berstatus `menunggu_konfirmasi` (baru ditentukan admin via WA setelah checkout).
> - Midtrans Snap membutuhkan nilai `gross_amount` (total tagihan) yang **pasti dan final** saat Snap Token di-generate.
> - **Opsi Keputusan**:
>   - **Opsi A (Rekomendasi Terbaik)**: Terapkan tarif ongkir flat berdasarkan cabang/kota (misal Rp 10.000 atau Rp 15.000) atau integrasikan kalkulasi jarak/API ongkir di muka, sehingga total tagihan sudah final 100% saat menekan tombol bayar Midtrans.
>   - **Opsi B (Two-Step Checkout)**: Khusus pesanan delivery, pembuatan pesanan via Midtrans tidak langsung memunculkan pop-up bayar, melainkan menunggu admin menginput ongkir di admin panel. Setelah ongkir dikonfirmasi admin, customer membuka halaman `status-pesanan` dan mengklik tombol "Bayar Sekarang via Midtrans".
>   - **Opsi C (Midtrans Khusus Pickup)**: Midtrans hanya diaktifkan untuk pesanan `pickup` (karena nominal pasti final), sedangkan pesanan `delivery` tetap memakai alur konfirmasi admin.

> [!IMPORTANT]
> ### 2. Batasan Instruksi `AGENTS.md`
> Pada `AGENTS.md` terdapat instruksi eksplisit:
> *"Payment: QRIS statis dengan verifikasi bukti bayar manual oleh admin (TAHAP SEKARANG). Midtrans Snap direncanakan sebagai fitur akhir — JANGAN implementasi webhook Midtrans dulu sebelum diminta eksplisit."*  
> *"Jangan implementasi Midtrans/payment gateway otomatis — masih manual QRIS statis untuk tahap ini."*  
> **Konsekuensi**: Segala bentuk penulisan kode Midtrans, migration penambahan kolom Snap, dan instalasi package hanya boleh dieksekusi setelah mendapat lampu hijau eksplisit dari user/klien.

> [!NOTE]
> ### 3. Pembersihan Controller Legacy
> Controller `app/Http/Controllers/PesananController.php` tidak digunakan oleh routing mana pun (karena rute aktif memakai `Customer\CheckoutController` dan `Customer\StatusPesananController`). Jika dibiarkan, file ini dapat menimbulkan kebingungan bagi developer di masa mendatang dan disarankan untuk diarsipkan atau dihapus saat refactoring berikutnya.

---
*Laporan ini disusun sebagai hasil audit menyeluruh terhadap kode, database, dan arsitektur aktif tanpa mengubah file kode yang sudah ada.*

