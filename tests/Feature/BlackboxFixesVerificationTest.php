<?php

namespace Tests\Feature;

use App\Models\Bahan;
use App\Models\Cabang;
use App\Models\KomponenSalad;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlackboxFixesVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_detail_produk_endpoint_returns_json_and_ingredients(): void
    {
        $cabang = Cabang::firstOrCreate(
            ['nama_cabang' => 'MERR Test'],
            ['alamat' => 'MERR Address', 'jam_buka' => '09:00:00', 'jam_tutup' => '21:00:00', 'layani_pickup' => true]
        );

        $produk = Produk::create([
            'nama_produk' => 'Rice Bowl MERR Test',
            'kategori' => 'Salad',
            'harga_offline' => 20000,
            'harga_online' => 25000,
            'status_aktif' => true,
            'id_cabang' => $cabang->id_cabang,
            'deskripsi' => 'Menu eksklusif cabang MERR',
        ]);

        $bahan = Bahan::create([
            'nama_bahan' => 'Beras Organik',
            'keterangan' => 'Bebas pestisida',
        ]);
        $produk->bahan()->attach($bahan->id_bahan);

        $response = $this->getJson(route('customer.produk.detail', $produk->id_produk));
        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('nama_produk', 'Rice Bowl MERR Test')
            ->assertJsonPath('cabang', 'MERR Test')
            ->assertJsonPath('bahan.0.nama', 'Beras Organik');
    }

    public function test_status_pesanan_with_invalid_code_displays_not_found_alert(): void
    {
        $response = $this->get(route('customer.status_pesanan', 'KODE-PALSU-99999'));
        $response->assertOk()
            ->assertSee('tidak ditemukan di sistem');
    }

    public function test_customer_rating_submission_stores_review_and_prevents_duplicate(): void
    {
        $cabang = Cabang::first() ?? Cabang::create([
            'nama_cabang' => 'Cabang Test',
            'alamat' => 'Alamat Test',
            'jam_buka' => '09:00:00',
            'jam_tutup' => '21:00:00',
            'layani_pickup' => true,
        ]);

        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-TEST-'.uniqid(),
            'jenis' => 'online',
            'nama_pelanggan' => 'Budi Tester',
            'no_whatsapp' => '081299998888',
            'metode_ambil' => 'pickup',
            'id_cabang' => $cabang->id_cabang,
            'ongkir' => 0,
            'status_ongkir' => 'tidak_perlu',
            'subtotal' => 25000,
            'total_diskon' => 0,
            'total_bayar' => 25000,
            'metode_bayar' => 'qris',
            'status_pembayaran' => 'terverifikasi',
            'status_pesanan' => 'selesai',
        ]);

        $response = $this->postJson(route('customer.status_pesanan.rating', $pesanan->kode_pesanan), [
            'bintang' => 5,
            'ulasan' => 'Salad sangat segar dan sausnya pas!',
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('rating', [
            'id_pesanan' => $pesanan->id_pesanan,
            'bintang' => 5,
            'ulasan' => 'Salad sangat segar dan sausnya pas!',
        ]);

        // Duplicate rating attempt should be rejected
        $duplicateResponse = $this->postJson(route('customer.status_pesanan.rating', $pesanan->kode_pesanan), [
            'bintang' => 4,
            'ulasan' => 'Coba kirim ulang ulasan',
        ]);

        $duplicateResponse->assertStatus(422);
    }

    public function test_checkout_pickup_persists_chosen_cabang(): void
    {
        $cabang1 = Cabang::firstOrCreate(
            ['nama_cabang' => 'Lidah Kulon'],
            ['alamat' => 'Jl. Lidah Kulon', 'jam_buka' => '09:00:00', 'jam_tutup' => '21:00:00', 'layani_pickup' => true]
        );
        $cabang2 = Cabang::firstOrCreate(
            ['nama_cabang' => 'MERR Test Outlet'],
            ['alamat' => 'Jl. MERR Surabaya', 'jam_buka' => '09:00:00', 'jam_tutup' => '21:00:00', 'layani_pickup' => true]
        );

        $produk = Produk::create([
            'nama_produk' => 'Green Salad Test',
            'kategori' => 'Salad',
            'harga_offline' => 20000,
            'harga_online' => 25000,
            'status_aktif' => true,
        ]);

        $payload = [
            'nama_pelanggan' => 'Siti Pickup',
            'no_whatsapp' => '081234567890',
            'metode_ambil' => 'pickup',
            'id_cabang' => $cabang2->id_cabang,
            'metode_bayar' => 'qris',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $produk->id_produk,
                    'jumlah' => 1,
                ],
            ],
        ];

        $response = $this->postJson(route('customer.checkout.store'), $payload);
        $response->assertStatus(201);

        $kode = $response->json('kode_pesanan');
        $this->assertDatabaseHas('pesanan', [
            'kode_pesanan' => $kode,
            'metode_ambil' => 'pickup',
            'id_cabang' => $cabang2->id_cabang,
        ]);
    }

    public function test_admin_can_toggle_komponen_salad_status(): void
    {
        $komponen = KomponenSalad::create([
            'nama_komponen' => 'Brokoli Segar Test',
            'jenis' => 'sayuran',
            'harga_tambahan' => 3000,
            'status_aktif' => true,
        ]);

        $this->withSession(['admin_id' => 1, 'admin_nama' => 'Admin Test']);

        $response = $this->patch(route('admin.kelola_menu.komponen.status', $komponen->id_komponen));
        $response->assertRedirect(route('admin.kelola_menu', ['tab' => 'komponen']));

        $this->assertDatabaseHas('komponen_salad', [
            'id_komponen' => $komponen->id_komponen,
            'status_aktif' => false,
        ]);
    }
}
