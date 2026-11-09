# Flat Delivery Fee & Checkout Unification Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Menetapkan tarif ongkir standar (flat rate) default Rp 2.000 yang dapat diset di Dashboard Admin Pengaturan, menghapus kebutuhan konfirmasi ongkir manual admin, dan menyatukan alur checkout QRIS Midtrans agar pesanan delivery dapat langsung membayar instan saat checkout.

**Architecture:** Kolom `tarif_ongkir` ditambahkan pada tabel `pengaturan` dengan default `2000.00`. `PengaturanController` menyediakan endpoint pembaruan tarif ongkir. `CheckoutController` langsung membebankan `ongkir` dan menandai `status_ongkir = 'terkonfirmasi'` pada saat submit pesanan delivery, serta langsung mengeksekusi `MidtransService::chargeQris` dengan total bayar final (sama seperti alur pickup).

**Tech Stack:** Laravel 13, Blade, MySQL/SQLite, Tailwind CSS, Midtrans Core API PHP SDK, PHPUnit.

---

### Task 1: Migration & Model Pengaturan (`tarif_ongkir`)

**Files:**
- Create: `database/migrations/2026_11_08_190000_add_tarif_ongkir_to_pengaturan_table.php`
- Modify: `app/Models/Pengaturan.php`
- Test: `tests/Feature/PengaturanOngkirTest.php`

- [ ] **Step 1: Write the failing test for `Pengaturan::getTarifOngkir()`**

Create `tests/Feature/PengaturanOngkirTest.php`:
```php
<?php

namespace Tests\Feature;

use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaturanOngkirTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_tarif_ongkir_returns_default_2000_when_no_record(): void
    {
        $this->assertEquals(2000.00, Pengaturan::getTarifOngkir());
    }

    public function test_get_tarif_ongkir_returns_stored_value(): void
    {
        Pengaturan::create([
            'tarif_ongkir' => 5000,
        ]);

        $this->assertEquals(5000.00, Pengaturan::getTarifOngkir());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_get_tarif_ongkir_returns_default_2000_when_no_record`
Expected: FAIL (`Column not found` or `Method does not exist`)

- [ ] **Step 3: Create migration and update Model**

Create `database/migrations/2026_11_08_190000_add_tarif_ongkir_to_pengaturan_table.php`:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->decimal('tarif_ongkir', 10, 2)->default(2000)->after('instagram');
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn('tarif_ongkir');
        });
    }
};
```

Update `app/Models/Pengaturan.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $primaryKey = 'id_pengaturan';

    protected $fillable = [
        'logo',
        'whatsapp',
        'instagram',
        'tarif_ongkir',
    ];

    protected $casts = [
        'tarif_ongkir' => 'float',
    ];

    public static function getTarifOngkir(): float
    {
        return (float) (self::value('tarif_ongkir') ?? 2000.00);
    }
}
```

- [ ] **Step 4: Run migration and verify test passes**

Run: `php artisan migrate`
Run: `php artisan test tests/Feature/PengaturanOngkirTest.php`
Expected: PASS (2 tests, 2 assertions)

---

### Task 2: Dashboard Admin Pengaturan Form (Tarif Pengiriman)

**Files:**
- Modify: `app/Http/Controllers/Admin/PengaturanController.php`
- Modify: `routes/web.php`
- Modify: `resources/views/admin/pengaturan.blade.php`
- Test: `tests/Feature/PengaturanOngkirTest.php`

- [ ] **Step 1: Write the failing test for updating `tarif_ongkir` via Admin**

Add to `tests/Feature/PengaturanOngkirTest.php`:
```php
    public function test_admin_can_update_tarif_ongkir(): void
    {
        $admin = \App\Models\Admin::create([
            'nama' => 'Admin Test',
            'username' => 'admin_test',
            'password' => bcrypt('password123'),
        ]);

        session(['admin_id' => $admin->id_admin]);

        $response = $this->post(route('admin.pengaturan.pengiriman'), [
            'tarif_ongkir' => 3500,
        ]);

        $response->assertRedirect(route('admin.pengaturan'));
        $this->assertEquals(3500.00, Pengaturan::getTarifOngkir());
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_admin_can_update_tarif_ongkir`
Expected: FAIL (Route not defined or 404)

- [ ] **Step 3: Implement controller method, route, and view section**

In `app/Http/Controllers/Admin/PengaturanController.php`:
```php
    public function updatePengiriman(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tarif_ongkir' => ['required', 'numeric', 'min:0'],
        ], [
            'tarif_ongkir.required' => 'Tarif ongkos kirim wajib diisi.',
            'tarif_ongkir.numeric' => 'Tarif ongkos kirim harus berupa angka.',
            'tarif_ongkir.min' => 'Tarif ongkos kirim minimal Rp 0.',
        ]);

        Pengaturan::firstOrCreate([], [
            'whatsapp' => '+6285812345678',
            'instagram' => '@hamta.sby',
            'tarif_ongkir' => 2000,
        ])->update([
            'tarif_ongkir' => $data['tarif_ongkir'],
        ]);

        return redirect()->route('admin.pengaturan')
            ->with('success', 'Tarif ongkos kirim berhasil diperbarui.');
    }
```

In `routes/web.php` (under `admin.auth` group):
```php
Route::post('/admin/pengaturan/pengiriman', [PengaturanController::class, 'updatePengiriman'])->name('admin.pengaturan.pengiriman');
```

In `resources/views/admin/pengaturan.blade.php`:
Tambahkan kartu form pengaturan tarif pengiriman di section pengaturan toko.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/PengaturanOngkirTest.php`
Expected: PASS (3 tests, 4 assertions)

---

### Task 3: Backend Checkout Delivery Flat Ongkir & QRIS Unification

**Files:**
- Modify: `app/Http/Controllers/Customer/CheckoutController.php`
- Test: `tests/Feature/MidtransIntegrationTest.php`

- [ ] **Step 1: Write failing test in `MidtransIntegrationTest.php` for delivery order charging Midtrans immediately**

Add/update in `tests/Feature/MidtransIntegrationTest.php`:
```php
    public function test_delivery_order_charges_midtrans_immediately_with_flat_ongkir(): void
    {
        $fakeQrUrl = 'https://api.sandbox.midtrans.com/v2/qris/deliv-instan/qr-code';

        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('chargeQris')
            ->once()
            ->withArgs(function ($orderId, $grossAmount) {
                // subtotal 30000 + default ongkir 2000 = 32000
                return str_starts_with($orderId, 'HMT-') && $grossAmount === 32000;
            })
            ->andReturn([
                'status_code' => '201',
                'transaction_id' => 'mid-deliv-32000',
                'order_id' => 'HMT-DELIV-32K',
                'gross_amount' => '32000.00',
                'payment_type' => 'qris',
                'transaction_status' => 'pending',
                'qr_url' => $fakeQrUrl,
            ]);

        $this->app->instance(MidtransService::class, $mockMidtrans);

        $payload = [
            'nama_pelanggan' => 'Siti Nurhaliza',
            'no_whatsapp' => '08198765432',
            'metode_ambil' => 'delivery',
            'alamat_delivery' => 'Jl. Kertajaya Indah No. 5, Surabaya',
            'metode_bayar' => 'qris',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->produk->id_produk,
                    'jumlah' => 1,
                ],
            ],
        ];

        $response = $this->postJson(route('customer.checkout.store'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'metode_bayar' => 'midtrans',
                'metode_ambil' => 'delivery',
                'status_pembayaran' => 'pending',
                'qr_url' => $fakeQrUrl,
            ]);

        $pesanan = Pesanan::where('nama_pelanggan', 'Siti Nurhaliza')->first();
        $this->assertNotNull($pesanan);
        $this->assertEquals(2000.00, (float) $pesanan->ongkir);
        $this->assertEquals('terkonfirmasi', $pesanan->status_ongkir);
        $this->assertEquals(32000.00, (float) $pesanan->total_bayar);
        $this->assertEquals('pending', $pesanan->status_pembayaran);
        $this->assertEquals('midtrans', $pesanan->metode_bayar);
    }
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=test_delivery_order_charges_midtrans_immediately_with_flat_ongkir`
Expected: FAIL

- [ ] **Step 3: Update `CheckoutController@store` & helper `buatPesananDanItem`**

In `app/Http/Controllers/Customer/CheckoutController.php`:
1. Ambil tarif ongkir jika `metode_ambil === 'delivery'`:
   `$ongkir = $data['metode_ambil'] === 'delivery' ? Pengaturan::getTarifOngkir() : 0.00;`
   `$statusOngkir = $data['metode_ambil'] === 'delivery' ? 'terkonfirmasi' : 'tidak_perlu';`
2. Unifikasi alur QRIS:
   Jika `in_array($data['metode_bayar'], ['qris', 'midtrans'])`:
   Baik `pickup` maupun `delivery` masuk ke blok `DB::beginTransaction()`, insert pesanan dengan ongkir & hitung promo, lalu panggil `chargeQris()`. Jika gagal -> `DB::rollBack()` dan return `fallback_required: true`.
3. Set `ongkir` dan `status_ongkir` langsung saat insert pesanan.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test tests/Feature/MidtransIntegrationTest.php`
Expected: PASS

---

### Task 4: Frontend Checkout UI Updates (Flat Ongkir Display)

**Files:**
- Modify: `resources/views/customer/checkout.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Pass `tarif_ongkir` to `customer.checkout` view**

In `routes/web.php`:
Ubah `Route::view('/checkout', 'customer.checkout')` menjadi:
```php
Route::get('/checkout', function () {
    return view('customer.checkout', [
        'tarifOngkir' => \App\Models\Pengaturan::getTarifOngkir(),
    ]);
})->name('customer.checkout');
```

- [ ] **Step 2: Update JS in `checkout.blade.php`**

1. Definisikan `var FLAT_ONGKIR = {{ $tarifOngkir ?? 2000 }};`.
2. Di fungsi `render()`:
   - Jika `take === 'delivery'`: biaya ongkir = `FLAT_ONGKIR`, dan total = `subtotal - totalDiskon + FLAT_ONGKIR`.
   - Update tampilan `#co-ongkir` dengan format rupiah.
3. Di `#qris-delivery-hint`:
   Hapus catatan "Kode QRIS akan terbit setelah admin konfirmasi ongkir" karena QRIS langsung terbit saat submit checkout.

- [ ] **Step 3: Run checkout feature tests**

Run: `php artisan test tests/Feature/MidtransIntegrationTest.php`
Expected: PASS

---

### Task 5: Admin Pesanan Detail & Customer Status Pesanan Cleanup

**Files:**
- Modify: `resources/views/admin/partials/pesanan_detail.blade.php`
- Modify: `resources/views/customer/status-pesanan.blade.php`
- Test: Full test suite `php artisan test`

- [ ] **Step 1: Clean up Admin Pesanan Detail View**

In `resources/views/admin/partials/pesanan_detail.blade.php`:
Hapus modal popup dan tombol "Konfirmasi Ongkir". Pesanan delivery selalu menampilkan ongkir yang terkonfirmasi.

- [ ] **Step 2: Clean up Customer Status Pesanan View**

In `resources/views/customer/status-pesanan.blade.php`:
Hapus elemen `#tr-midtrans-delivery-waiting` ("Menunggu Konfirmasi Ongkir: Admin sedang menghitung tarif pengiriman...") karena pesanan delivery sekarang langsung terbit QRIS-nya saat dibuat.

- [ ] **Step 3: Run complete test suite and code formatter**

Run: `vendor/bin/pint --dirty --format agent`
Run: `php artisan test`
Expected: All tests PASS (60+ tests passing, 0 failures)

