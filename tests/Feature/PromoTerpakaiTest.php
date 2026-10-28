<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\Promo;
use App\Models\Produk;
use App\Services\PromoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoTerpakaiTest extends TestCase
{
    use RefreshDatabase;

    private function promo(array $atribut = []): Promo
    {
        return Promo::create(array_merge([
            'nama_promo' => 'Promo Uji',
            'tipe_potongan' => 'nominal',
            'nilai_potongan' => 5000,
            'batas_penggunaan' => 10,
            'jumlah_terpakai' => 0,
            'status_aktif' => true,
        ], $atribut));
    }

    private function pesanan(array $atribut = []): Pesanan
    {
        $cabang = Cabang::create([
            'nama_cabang' => 'Cabang Uji',
            'alamat' => 'Jl. Uji',
            'jam_buka' => '08:00',
            'jam_tutup' => '21:00',
        ]);

        return Pesanan::create(array_merge([
            'kode_pesanan' => 'HMT-' . uniqid(),
            'jenis' => 'kasir',
            'id_cabang' => $cabang->id_cabang,
            'metode_ambil' => 'pickup',
            'ongkir' => 0,
            'status_ongkir' => 'tidak_perlu',
            'subtotal' => 50000,
            'total_diskon' => 0,
            'total_bayar' => 50000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'terverifikasi',
            'status_pesanan' => 'diproses',
        ], $atribut));
    }

    // --- promoLayak ---

    public function test_promo_aktif_dan_kuota_tersedia_layak(): void
    {
        $promo = $this->promo();

        $layak = app(PromoService::class)->promoLayak($promo, 50000, 10, 'pickup');

        $this->assertTrue($layak);
    }

    public function test_promo_nonaktif_tidak_layak(): void
    {
        $promo = $this->promo(['status_aktif' => false]);

        $this->assertFalse(app(PromoService::class)->promoLayak($promo, 50000, 10, 'pickup'));
    }

    public function test_promo_kuota_habis_tidak_layak(): void
    {
        $promo = $this->promo(['batas_penggunaan' => 5, 'jumlah_terpakai' => 5]);

        $this->assertFalse(app(PromoService::class)->promoLayak($promo, 50000, 10, 'pickup'));
    }

    public function test_promo_luar_periode_tidak_layak(): void
    {
        $promo = $this->promo([
            'tanggal_mulai' => now()->addDays(3)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
        ]);

        $this->assertFalse(app(PromoService::class)->promoLayak($promo, 50000, 10, 'pickup'));
    }

    public function test_promo_dalam_periode_layak(): void
    {
        $promo = $this->promo([
            'tanggal_mulai' => now()->subDays(3)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
        ]);

        $this->assertTrue(app(PromoService::class)->promoLayak($promo, 50000, 10, 'pickup'));
    }

    public function test_syarat_min_porsi_tidak_terpenuhi_tidak_layak(): void
    {
        $promo = $this->promo(['syarat_min_porsi' => 20]);

        $this->assertFalse(app(PromoService::class)->promoLayak($promo, 50000, 10, 'pickup'));
        $this->assertTrue(app(PromoService::class)->promoLayak($promo, 50000, 20, 'pickup'));
    }

    public function test_gratis_ongkir_hanya_layak_untuk_delivery(): void
    {
        $promo = $this->promo(['tipe_potongan' => 'gratis_ongkir', 'nilai_potongan' => null]);

        $this->assertFalse(app(PromoService::class)->promoLayak($promo, 50000, 10, 'pickup'));
        $this->assertTrue(app(PromoService::class)->promoLayak($promo, 50000, 10, 'delivery'));
    }

    // --- hitungPotongan ---

    public function test_potongan_nominal(): void
    {
        $promo = $this->promo(['nilai_potongan' => 5000]);

        $this->assertSame(5000.0, app(PromoService::class)->hitungPotongan($promo, 50000));
    }

    public function test_potongan_persen(): void
    {
        $promo = $this->promo(['tipe_potongan' => 'persen', 'nilai_potongan' => 10]);

        $this->assertSame(5000.0, app(PromoService::class)->hitungPotongan($promo, 50000));
    }

    public function test_potongan_tidak_boleh_melebihi_subtotal(): void
    {
        $promo = $this->promo(['nilai_potongan' => 999999]);

        $this->assertSame(50000.0, app(PromoService::class)->hitungPotongan($promo, 50000));
    }

    // --- terapkanKePesanan ---

    public function test_promo_nominal_terpakai_dan_kuota_naik(): void
    {
        $promo = $this->promo(['nilai_potongan' => 5000]);
        $pesanan = $this->pesanan();

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        $pesanan->refresh();
        $promo->refresh();

        $this->assertSame(5000.0, (float) $pesanan->total_diskon);
        $this->assertSame(45000.0, (float) $pesanan->total_bayar);
        $this->assertSame(1, $promo->jumlah_terpakai);
        $this->assertCount(1, $pesanan->promo);
    }

    public function test_promo_persen_terpakai(): void
    {
        $promo = $this->promo(['tipe_potongan' => 'persen', 'nilai_potongan' => 10]);
        $pesanan = $this->pesanan();

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        $pesanan->refresh();

        $this->assertSame(5000.0, (float) $pesanan->total_diskon);
        $this->assertSame(45000.0, (float) $pesanan->total_bayar);
    }

    public function test_stacking_banyak_promo(): void
    {
        $this->promo(['nama_promo' => 'A', 'nilai_potongan' => 5000]);
        $this->promo(['nama_promo' => 'B', 'nilai_potongan' => 3000]);
        $pesanan = $this->pesanan();

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        $pesanan->refresh();

        $this->assertSame(8000.0, (float) $pesanan->total_diskon);
        $this->assertSame(42000.0, (float) $pesanan->total_bayar);
        $this->assertCount(2, $pesanan->promo);
    }

    public function test_promo_tidak_layak_tidak_terpakai(): void
    {
        $this->promo(['syarat_min_porsi' => 100]);
        $pesanan = $this->pesanan();

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        $pesanan->refresh();

        $this->assertSame(0.0, (float) $pesanan->total_diskon);
        $this->assertCount(0, $pesanan->promo);
    }

    public function test_kuota_habis_otomatis_nonaktif_promo(): void
    {
        $promo = $this->promo(['batas_penggunaan' => 1]);
        $pesanan = $this->pesanan();

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        $promo->refresh();

        $this->assertSame(1, $promo->jumlah_terpakai);
        $this->assertFalse($promo->status_aktif);
    }

    public function test_promo_kehabisan_kuota_tidak_lagi_dipakai(): void
    {
        $promo = $this->promo(['batas_penggunaan' => 1]);
        $promo->update(['jumlah_terpakai' => 1]);

        $pesanan = $this->pesanan();
        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        $pesanan->refresh();
        $promo->refresh();

        $this->assertSame(1, $promo->jumlah_terpakai);
        $this->assertCount(0, $pesanan->promo);
    }

    public function test_gratis_ongkir_nilai_0_sampai_ongkir_dikonfirmasi(): void
    {
        $promo = $this->promo(['tipe_potongan' => 'gratis_ongkir', 'nilai_potongan' => null]);
        $pesanan = $this->pesanan([
            'metode_ambil' => 'delivery',
            'ongkir' => null,
            'status_ongkir' => 'menunggu_konfirmasi',
        ]);

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        $pesanan->refresh();
        $promo->refresh();

        // Kuota sudah terpakai, tapi nilai diskon masih 0 (ongkir belum dikonfirmasi)
        $this->assertSame(1, $promo->jumlah_terpakai);
        $this->assertSame(0.0, (float) $pesanan->total_diskon);
    }

    // --- terapkanUlang ---

    public function test_hitung_ulang_setelah_ongkir_dikonfirmasi(): void
    {
        $this->promo(['tipe_potongan' => 'gratis_ongkir', 'nilai_potongan' => null]);
        $pesanan = $this->pesanan([
            'metode_ambil' => 'delivery',
            'ongkir' => null,
            'status_ongkir' => 'menunggu_konfirmasi',
        ]);

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        $pesanan->update(['ongkir' => 15000, 'status_ongkir' => 'terkonfirmasi']);
        app(PromoService::class)->terapkanUlang($pesanan);

        $pesanan->refresh();

        // Gratis ongkir = 15000, total bayar = subtotal - diskon + ongkir = 50000
        $this->assertSame(15000.0, (float) $pesanan->total_diskon);
        $this->assertSame(50000.0, (float) $pesanan->total_bayar);
    }

    public function test_hitung_ulang_setelah_item_dibatalkan(): void
    {
        $this->promo(['tipe_potongan' => 'persen', 'nilai_potongan' => 10]);
        $pesanan = $this->pesanan();

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        // Subtotal turun jadi 20000 → diskon 10% = 2000
        app(PromoService::class)->terapkanUlang($pesanan, 20000);

        $pesanan->refresh();

        $this->assertSame(20000.0, (float) $pesanan->subtotal);
        $this->assertSame(2000.0, (float) $pesanan->total_diskon);
        $this->assertSame(18000.0, (float) $pesanan->total_bayar);
    }

    public function test_total_bayar_tidak_boleh_negatif(): void
    {
        $this->promo(['nilai_potongan' => 50000]);
        $pesanan = $this->pesanan();

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);

        // Subtotal turun drastis, diskon nominal melebihi sisa subtotal
        app(PromoService::class)->terapkanUlang($pesanan, 1000);

        $pesanan->refresh();

        // Diskon dipangkas ke sisa subtotal (1000), total bayar tidak boleh minus
        $this->assertSame(1000.0, (float) $pesanan->total_diskon);
        $this->assertSame(0.0, (float) $pesanan->total_bayar);
    }

    public function test_snapshot_pivot_diupdate_saat_hitung_ulang(): void
    {
        $this->promo(['tipe_potongan' => 'persen', 'nilai_potongan' => 10]);
        $pesanan = $this->pesanan();

        app(PromoService::class)->terapkanKePesanan($pesanan, 10);
        app(PromoService::class)->terapkanUlang($pesanan, 20000);

        $pesanan->refresh();

        $this->assertSame(2000.0, (float) $pesanan->promo->first()->pivot->nilai_potongan);
    }
}
