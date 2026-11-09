<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Cabang;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class MidtransIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Cabang $cabang;

    protected Produk $produk;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.midtrans.server_key' => 'test-server-key-12345',
            'services.midtrans.client_key' => 'test-client-key-12345',
            'services.midtrans.is_production' => false,
        ]);

        $this->cabang = Cabang::create([
            'nama_cabang' => 'Cabang Merr',
            'alamat' => 'Jl. Merr No. 1, Surabaya',
            'jam_buka' => '08:00',
            'jam_tutup' => '21:00',
            'layani_pickup' => true,
            'layani_dinein' => false,
        ]);

        $this->produk = Produk::create([
            'nama_produk' => 'Green Goddess Salad',
            'deskripsi' => 'Salad sayur segar',
            'kategori' => 'Salad',
            'harga_offline' => 25000,
            'harga_online' => 30000,
            'status_aktif' => true,
            'bisa_dikustom' => false,
            'id_cabang' => $this->cabang->id_cabang,
        ]);
    }

    public function test_signature_verification_succeeds_and_fails(): void
    {
        $service = new MidtransService;
        $orderId = 'HMT-261108-TEST1';
        $statusCode = '200';
        $grossAmount = '30000.00';
        $validSignature = hash('sha512', $orderId.$statusCode.$grossAmount.'test-server-key-12345');

        $this->assertTrue($service->verifySignature($orderId, $statusCode, $grossAmount, $validSignature));
        $this->assertFalse($service->verifySignature($orderId, $statusCode, $grossAmount, 'invalid-signature'));
    }

    public function test_pickup_order_with_midtrans_charges_immediately(): void
    {
        $fakeQrUrl = 'https://api.sandbox.midtrans.com/v2/qris/12345/qr-code';

        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('chargeQris')
            ->once()
            ->withArgs(function ($orderId, $grossAmount) {
                return str_starts_with($orderId, 'HMT-') && $grossAmount === 30000;
            })
            ->andReturn([
                'status_code' => '201',
                'transaction_id' => 'mid-txn-123',
                'order_id' => 'HMT-123',
                'gross_amount' => '30000.00',
                'payment_type' => 'qris',
                'transaction_status' => 'pending',
                'qr_url' => $fakeQrUrl,
            ]);

        $this->app->instance(MidtransService::class, $mockMidtrans);

        $payload = [
            'nama_pelanggan' => 'Budi Santoso',
            'no_whatsapp' => '08123456789',
            'metode_ambil' => 'pickup',
            'metode_bayar' => 'midtrans',
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
                'metode_ambil' => 'pickup',
                'status_pembayaran' => 'pending',
                'qr_url' => $fakeQrUrl,
            ]);

        $this->assertDatabaseHas('pesanan', [
            'nama_pelanggan' => 'Budi Santoso',
            'metode_bayar' => 'midtrans',
            'metode_ambil' => 'pickup',
            'status_pembayaran' => 'pending',
        ]);
    }

    public function test_delivery_order_charges_midtrans_immediately_with_flat_ongkir(): void
    {
        $fakeQrUrl = 'https://api.sandbox.midtrans.com/v2/qris/delivery-flat/qr-code';

        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('chargeQris')
            ->once()
            ->withArgs(function ($orderId, $grossAmount) {
                // subtotal 30000 + default flat ongkir 2000 = 32000
                return str_starts_with($orderId, 'HMT-') && $grossAmount === 32000;
            })
            ->andReturn([
                'status_code' => '201',
                'transaction_id' => 'mid-txn-deliv-32000',
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
            'metode_bayar' => 'midtrans',
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

    public function test_webhook_settlement_updates_status_to_terverifikasi(): void
    {
        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-261108-ABCDE',
            'jenis' => 'online',
            'nama_pelanggan' => 'Rina',
            'no_whatsapp' => '0812345678',
            'metode_ambil' => 'pickup',
            'id_cabang' => $this->cabang->id_cabang,
            'subtotal' => 30000,
            'total_bayar' => 30000,
            'metode_bayar' => 'midtrans',
            'midtrans_order_id' => 'HMT-261108-ABCDE-12345',
            'status_pembayaran' => 'pending',
            'status_pesanan' => 'diproses',
        ]);

        $orderId = 'HMT-261108-ABCDE-12345';
        $statusCode = '200';
        $grossAmount = '30000.00';
        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.'test-server-key-12345');

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
        ];

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'status_pembayaran' => 'terverifikasi',
            ]);

        $pesanan->refresh();
        $this->assertEquals('terverifikasi', $pesanan->status_pembayaran);
        $this->assertEquals('settlement', $pesanan->midtrans_response['last_transaction_status']);
    }

    public function test_webhook_expire_updates_status_to_ditolak(): void
    {
        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-261108-XYZ12',
            'jenis' => 'online',
            'nama_pelanggan' => 'Andi',
            'no_whatsapp' => '0812345679',
            'metode_ambil' => 'pickup',
            'id_cabang' => $this->cabang->id_cabang,
            'subtotal' => 30000,
            'total_bayar' => 30000,
            'metode_bayar' => 'midtrans',
            'midtrans_order_id' => 'HMT-261108-XYZ12-99999',
            'status_pembayaran' => 'pending',
            'status_pesanan' => 'diproses',
        ]);

        $orderId = 'HMT-261108-XYZ12-99999';
        $statusCode = '200';
        $grossAmount = '30000.00';
        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.'test-server-key-12345');

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
            'payment_type' => 'qris',
        ];

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertStatus(200);

        $pesanan->refresh();
        $this->assertEquals('ditolak', $pesanan->status_pembayaran);
    }

    public function test_webhook_invalid_signature_returns_403(): void
    {
        $payload = [
            'order_id' => 'HMT-UNKNOWN-123',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => 'fake_signature_not_matching',
            'transaction_status' => 'settlement',
        ];

        $response = $this->postJson(route('midtrans.notification'), $payload);

        $response->assertStatus(403);
    }

    public function test_status_pesanan_api_includes_qr_url(): void
    {
        $fakeQrUrl = 'https://api.sandbox.midtrans.com/v2/qris/api-test/qr-code';

        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-261108-LIVE1',
            'jenis' => 'online',
            'nama_pelanggan' => 'Dewi',
            'no_whatsapp' => '0812345670',
            'metode_ambil' => 'pickup',
            'id_cabang' => $this->cabang->id_cabang,
            'subtotal' => 30000,
            'total_bayar' => 30000,
            'metode_bayar' => 'midtrans',
            'midtrans_order_id' => 'HMT-261108-LIVE1-1',
            'midtrans_response' => [
                'qr_url' => $fakeQrUrl,
            ],
            'status_pembayaran' => 'pending',
            'status_pesanan' => 'diproses',
        ]);

        $response = $this->getJson(route('customer.pesanan.api_status', $pesanan->kode_pesanan));

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'kode_pesanan' => 'HMT-261108-LIVE1',
                'metode_bayar' => 'midtrans',
                'status_pembayaran' => 'pending',
                'qr_url' => $fakeQrUrl,
            ]);
    }

    public function test_pickup_with_qris_rolls_back_and_returns_fallback_required_when_midtrans_fails(): void
    {
        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('chargeQris')
            ->once()
            ->andThrow(new \RuntimeException('Midtrans connection timeout or service down'));

        $this->app->instance(MidtransService::class, $mockMidtrans);

        $payload = [
            'nama_pelanggan' => 'Doni Fallback',
            'no_whatsapp' => '081299998888',
            'metode_ambil' => 'pickup',
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

        $response->assertStatus(200)
            ->assertJson([
                'fallback_required' => true,
            ]);

        // Verifikasi DB bersih: 0 pesanan tersimpan di DB
        $this->assertDatabaseCount('pesanan', 0);
        $this->assertDatabaseCount('detail_pesanan', 0);
    }

    public function test_pickup_qris_statis_submission_with_bukti_bayar_persists_order(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('bukti_transfer.jpg');

        $payload = [
            'nama_pelanggan' => 'Eko Static',
            'no_whatsapp' => '081277776666',
            'metode_ambil' => 'pickup',
            'metode_bayar' => 'qris_statis',
            'bukti_bayar' => $file,
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->produk->id_produk,
                    'jumlah' => 1,
                ],
            ],
        ];

        $response = $this->post(route('customer.checkout.store'), $payload, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'metode_bayar' => 'qris_statis',
                'metode_ambil' => 'pickup',
                'status_pembayaran' => 'menunggu_verifikasi',
            ]);

        $pesanan = Pesanan::where('nama_pelanggan', 'Eko Static')->first();
        $this->assertNotNull($pesanan);
        $this->assertEquals('qris_statis', $pesanan->metode_bayar);
        $this->assertEquals('menunggu_verifikasi', $pesanan->status_pembayaran);
        $this->assertNotNull($pesanan->bukti_bayar);
        Storage::disk('public')->assertExists($pesanan->bukti_bayar);
    }

    public function test_delivery_konfirmasi_ongkir_falls_back_to_qris_statis_when_midtrans_fails(): void
    {
        $admin = Admin::create([
            'nama' => 'Admin Checker',
            'username' => 'admin_checker',
            'password' => bcrypt('secret123'),
        ]);

        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-DELIV-FALLBACK',
            'jenis' => 'online',
            'nama_pelanggan' => 'Fani Delivery',
            'no_whatsapp' => '081255554444',
            'metode_ambil' => 'delivery',
            'alamat_delivery' => 'Jl. Manyar Kertoarjo No. 12',
            'id_cabang' => $this->cabang->id_cabang,
            'subtotal' => 30000,
            'total_bayar' => 30000,
            'metode_bayar' => 'qris',
            'status_ongkir' => 'menunggu_konfirmasi',
            'status_pembayaran' => 'menunggu_verifikasi',
            'status_pesanan' => 'diproses',
        ]);

        $mockMidtrans = Mockery::mock(MidtransService::class);
        $mockMidtrans->shouldReceive('chargeQris')
            ->once()
            ->andThrow(new \RuntimeException('Midtrans gateway error'));

        $this->app->instance(MidtransService::class, $mockMidtrans);

        $this->actingAsAdmin($admin);

        $response = $this->patch(route('admin.daftar_pesanan.ongkir', $pesanan->id_pesanan), [
            'ongkir' => 12000,
        ]);

        $pesanan->refresh();
        $this->assertEquals(12000, (float) $pesanan->ongkir);
        $this->assertEquals('terkonfirmasi', $pesanan->status_ongkir);
        $this->assertEquals('qris_statis', $pesanan->metode_bayar);
        $this->assertEquals('menunggu_verifikasi', $pesanan->status_pembayaran);
    }

    public function test_upload_bukti_bayar_endpoint_updates_pesanan(): void
    {
        Storage::fake('public');

        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-UPLOAD-BUKTI',
            'jenis' => 'online',
            'nama_pelanggan' => 'Gita Bukti',
            'no_whatsapp' => '081233332222',
            'metode_ambil' => 'delivery',
            'id_cabang' => $this->cabang->id_cabang,
            'subtotal' => 30000,
            'ongkir' => 10000,
            'total_bayar' => 40000,
            'metode_bayar' => 'qris_statis',
            'status_ongkir' => 'terkonfirmasi',
            'status_pembayaran' => 'menunggu_verifikasi',
            'status_pesanan' => 'diproses',
            'bukti_bayar' => null,
        ]);

        $file = UploadedFile::fake()->image('bukti_tf.jpg');

        $response = $this->post(route('customer.status_pesanan.upload_bukti', $pesanan->kode_pesanan), [
            'bukti_bayar' => $file,
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'message' => 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi kasir.',
            ]);

        $pesanan->refresh();
        $this->assertNotNull($pesanan->bukti_bayar);
        $this->assertEquals('qris_statis', $pesanan->metode_bayar);
        $this->assertEquals('menunggu_verifikasi', $pesanan->status_pembayaran);
        Storage::disk('public')->assertExists($pesanan->bukti_bayar);
    }

    public function test_status_pesanan_api_includes_expiry_time_and_detects_expiration(): void
    {
        $fakeQrUrl = 'https://api.sandbox.midtrans.com/v2/qris/api-test/qr-code';
        $pastExpiry = now()->subMinutes(5)->format('Y-m-d H:i:s');

        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-261108-EXP1',
            'jenis' => 'online',
            'nama_pelanggan' => 'Sinta',
            'no_whatsapp' => '0812345671',
            'metode_ambil' => 'pickup',
            'id_cabang' => $this->cabang->id_cabang,
            'subtotal' => 30000,
            'total_bayar' => 30000,
            'metode_bayar' => 'midtrans',
            'midtrans_order_id' => 'HMT-261108-EXP1-1',
            'midtrans_response' => [
                'qr_url' => $fakeQrUrl,
                'expiry_time' => $pastExpiry,
            ],
            'status_pembayaran' => 'pending',
            'status_pesanan' => 'diproses',
        ]);

        $response = $this->getJson(route('customer.pesanan.api_status', $pesanan->kode_pesanan));

        $response->assertStatus(200)
            ->assertJson([
                'ok' => true,
                'kode_pesanan' => 'HMT-261108-EXP1',
                'qr_url' => $fakeQrUrl,
                'expiry_time' => $pastExpiry,
                'is_expired' => true,
            ]);
    }

    protected function actingAsAdmin(Admin $admin): self
    {
        session(['admin_id' => $admin->id_admin]);

        return $this;
    }
}
