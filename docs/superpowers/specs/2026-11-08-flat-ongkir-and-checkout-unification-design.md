# Design Specification: Flat Delivery Fee & Checkout Unification

- **Date:** 2026-11-08
- **Status:** Approved
- **Scope:** Pengaturan Tarif Ongkir Flat, Dashboard Admin Pengaturan, Unifikasi Alur Checkout & Pembayaran Delivery

---

## 1. Background & Goals

Sebelumnya, pesanan delivery menggunakan mekanisme two-step: tarif ongkir ditentukan manual oleh admin setelah checkout (`status_ongkir = 'menunggu_konfirmasi'`, `ongkir = null`), sehingga total tagihan belum final saat checkout dan charge pembayaran Midtrans QRIS baru bisa dilakukan setelah admin mengonfirmasi ongkir di dashboard.

Perubahan ini bertujuan untuk:
1. Menetapkan tarif ongkir standar (flat rate) dengan nilai default **Rp 2.000**, yang dapat diubah kapan saja oleh admin melalui menu Pengaturan.
2. Menghilangkan kebutuhan konfirmasi ongkir manual oleh admin. Setiap pesanan delivery otomatis memiliki ongkir terkonfirmasi saat checkout dibuat.
3. Menyatukan (unifikasi) alur pembayaran QRIS untuk Pickup dan Delivery: kedua metode kini menghitung total bayar final seketika di checkout dan langsung mencoba charge Midtrans QRIS tanpa jeda.

---

## 2. Database & Model

### 2.1. Migrasi Tabel `pengaturan`
- File migrasi: `database/migrations/YYYY_MM_DD_HHMMSS_add_tarif_ongkir_to_pengaturan_table.php`
- Kolom yang ditambahkan:
  - `tarif_ongkir`: `DECIMAL(10, 2)`, default `2000.00`, nullable `false`.

### 2.2. Model `App\Models\Pengaturan`
- `$fillable`: Menambahkan `'tarif_ongkir'`.
- `$casts`: Menambahkan `'tarif_ongkir' => 'float'`.
- Static Helper Method:
  ```php
  public static function getTarifOngkir(): float
  {
      return (float) (self::value('tarif_ongkir') ?? 2000.00);
  }
  ```

---

## 3. Dashboard Admin

### 3.1. Menu Pengaturan (`/admin/pengaturan`)
- **Controller (`Admin\PengaturanController`)**:
  - Method `updatePengiriman(Request $request): RedirectResponse`
  - Validasi: `'tarif_ongkir' => ['required', 'numeric', 'min:0']`
  - Menyimpan ke tabel `pengaturan`.
- **View (`resources/views/admin/pengaturan.blade.php`)**:
  - Menambahkan kartu/section form baru: **"Tarif Pengiriman (Delivery)"**.
  - Input: `tarif_ongkir` bertipe number dengan step `500` / integer, nilai awal dari `$pengaturan->tarif_ongkir ?? 2000`.
  - Tombol simpan: *"Simpan Tarif Pengiriman"*.
- **Route (`routes/web.php`)**:
  - `POST /admin/pengaturan/pengiriman` mengarah ke `PengaturanController@updatePengiriman` dengan nama `admin.pengaturan.pengiriman`.

### 3.2. Pembersihan Konfirmasi Ongkir di Daftar Pesanan
- **View Partial (`resources/views/admin/partials/pesanan_detail.blade.php`)**:
  - Hapus tombol/modal popup *"Konfirmasi Ongkir"*.
  - Pesanan delivery langsung menampilkan ongkir yang terkonfirmasi (contoh: `Rp 2.000 (Terkonfirmasi)`).
- **Controller (`Admin\PesananController`)**:
  - Method `konfirmasiOngkir()` tidak lagi dibutuhkan dalam alur normal, namun dapat dipertahankan atau disederhanakan tanpa memanggil Midtrans secara tertunda.

---

## 4. Alur Checkout Pelanggan & Pembayaran

### 4.1. Tampilan Checkout (`resources/views/customer/checkout.blade.php`)
- View menerima variabel `$tarifOngkir = \App\Models\Pengaturan::getTarifOngkir();` (atau via route provider/controller).
- Javascript checkout menggunakan variabel `window.HAMTA_DEFAULT_ONGKIR = {{ $tarifOngkir }};`.
- Saat pelanggan memilih opsi **Delivery**:
  - Biaya pengiriman di rincian otomatis terisi `Rp 2.000` (atau sesuai nilai dari pengaturan).
  - Total pembayaran langsung terhitung: `subtotal - diskon + ongkir`.
  - Label tombol submit: *"Bayar via QRIS & Buat Pesanan"* (jika QRIS) atau *"Buat Pesanan (Bayar di Tempat)"* (jika Tunai).

### 4.2. Logika Backend Checkout (`Customer\CheckoutController@store`)
- Jika `metode_ambil === 'delivery'`:
  - `ongkir = \App\Models\Pengaturan::getTarifOngkir()`
  - `status_ongkir = 'terkonfirmasi'`
- Jika `metode_ambil === 'pickup'`:
  - `ongkir = 0`
  - `status_ongkir = 'tidak_perlu'`
- Total bayar selalu final saat submit di semua metode pengambilan.

### 4.3. Unifikasi Pembayaran QRIS Midtrans
- Jika `in_array($data['metode_bayar'], ['qris', 'midtrans'])`:
  - Jalankan dalam `DB::beginTransaction()`.
  - Insert pesanan & item sementara untuk menghitung diskon/promo secara akurat via `PromoService::terapkanKePesanan()`.
  - Panggil `MidtransService::chargeQris($midtransOrderId, (int) round($pesanan->total_bayar))`.
  - **Jika Sukses**:
    - Update `metode_bayar = 'midtrans'`, `midtrans_order_id`, `midtrans_response`, `status_pembayaran = 'pending'`.
    - `DB::commit()`.
    - Return JSON response 201 dengan `qr_url`.
  - **Jika Gagal/Timeout**:
    - `DB::rollBack()` (0 baris tersimpan).
    - Return JSON `{"fallback_required": true}`.
    - Frontend checkout memunculkan barcode QRIS statis Hamta + form unggah bukti transfer tanpa reload halaman.
- Jika `metode_bayar === 'tunai'`:
  - Insert pesanan dengan `status_pembayaran = 'menunggu_verifikasi'`.

### 4.4. Halaman Status Pesanan (`customer.status-pesanan`)
- Hapus elemen `#tr-midtrans-delivery-waiting` ("Menunggu Konfirmasi Ongkir") karena pesanan delivery sudah memiliki nominal ongkir dan kode QRIS yang aktif sejak awal.

---

## 5. Testing & Regression Strategy

1. **Unit & Feature Tests**:
   - Update `tests/Feature/MidtransIntegrationTest.php` untuk menguji alur delivery yang kini langsung meng-generate QRIS saat checkout.
   - Test pengaturan tarif ongkir oleh admin di `/admin/pengaturan/pengiriman`.
   - Test pesanan delivery otomatis menggunakan tarif ongkir dari `Pengaturan`.
   - Jalankan seluruh suite test (`php artisan test`) untuk memastikan tidak ada test yang break.

