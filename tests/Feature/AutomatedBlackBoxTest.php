<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Cabang;
use App\Models\DetailPesanan;
use App\Models\KomponenSalad;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AutomatedBlackBoxTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected Cabang $cabangLidah;

    protected Cabang $cabangMerr;

    protected Produk $saladSayur;

    protected Produk $chefsSalad;

    protected Produk $riceBowl;

    protected KomponenSalad $kompSelada;

    protected KomponenSalad $kompTomat;

    protected KomponenSalad $kompChicken;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // 1. Setup Cabang
        $this->cabangLidah = Cabang::create([
            'nama_cabang' => 'Hamta Lidah Kulon',
            'alamat' => 'Jl. Lidah Kulon No. 10',
            'jam_buka' => '09:00',
            'jam_tutup' => '21:00',
            'layani_pickup' => true,
            'layani_dinein' => false,
        ]);

        $this->cabangMerr = Cabang::create([
            'nama_cabang' => 'Hamta MERR',
            'alamat' => 'Apartemen Bale Hinggil Lt. Ground',
            'jam_buka' => '09:00',
            'jam_tutup' => '21:00',
            'layani_pickup' => true,
            'layani_dinein' => true,
        ]);

        // 2. Setup Admin
        $this->admin = Admin::create([
            'nama' => 'Kak Yunita',
            'username' => 'admin',
            'password' => Hash::make('hamta'),
        ]);

        // 3. Setup Produk
        $this->saladSayur = Produk::create([
            'nama_produk' => 'Salad Sayur Segar',
            'deskripsi' => 'Salad segar dengan aneka sayuran hijau pilihan.',
            'kategori' => 'Salad',
            'harga_offline' => 20000,
            'harga_online' => 25000,
            'status_aktif' => true,
            'bisa_dikustom' => true,
            'id_cabang' => null,
        ]);

        $this->chefsSalad = Produk::create([
            'nama_produk' => "Chef's Salad Special",
            'deskripsi' => 'Resep paten dari Chef Hamta, komposisi tetap.',
            'kategori' => 'Salad',
            'harga_offline' => 30000,
            'harga_online' => 35000,
            'status_aktif' => true,
            'bisa_dikustom' => false, // TIDAK BISA DIKUSTOM
            'id_cabang' => null,
        ]);

        $this->riceBowl = Produk::create([
            'nama_produk' => 'Rice Bowl Teriyaki',
            'deskripsi' => 'Rice bowl hangat khusus dine in & take away MERR.',
            'kategori' => 'Salad', // atau lainnya
            'harga_offline' => 22000,
            'harga_online' => 27000,
            'status_aktif' => true,
            'bisa_dikustom' => false,
            'id_cabang' => $this->cabangMerr->id_cabang, // Eksklusif MERR
        ]);

        // 4. Setup Komponen Salad
        $this->kompSelada = KomponenSalad::create([
            'nama_komponen' => 'Selada Hijau',
            'jenis' => 'sayuran',
            'harga_tambahan' => 0,
            'status_aktif' => true,
        ]);

        $this->kompTomat = KomponenSalad::create([
            'nama_komponen' => 'Tomat Segar',
            'jenis' => 'sayuran',
            'harga_tambahan' => 0,
            'status_aktif' => true,
        ]);

        $this->kompChicken = KomponenSalad::create([
            'nama_komponen' => 'Chicken Patties',
            'jenis' => 'protein',
            'harga_tambahan' => 7000,
            'status_aktif' => true,
        ]);

        // 5. Setup Pengaturan
        Pengaturan::create([
            'whatsapp' => '+6285812345678',
            'instagram' => '@hamta.sby',
            'tarif_ongkir' => 5000,
        ]);
    }

    // ==========================================
    // A. CUSTOMER: LANDING PAGE & KATALOG
    // ==========================================

    public function test_landing_page_renders_active_products_and_hides_inactive(): void
    {
        $inactive = Produk::create([
            'nama_produk' => 'Menu Nonaktif Rahasia',
            'kategori' => 'Sandwich',
            'harga_offline' => 15000,
            'harga_online' => 18000,
            'status_aktif' => false,
            'bisa_dikustom' => false,
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Salad Sayur Segar');
        $response->assertDontSee('Menu Nonaktif Rahasia');
    }

    public function test_katalog_menu_renders_and_verifies_branch_exclusive_badge(): void
    {
        $response = $this->get(route('customer.daftar_menu'));
        $response->assertStatus(200);
        $response->assertSee('Salad Sayur Segar');
        $response->assertSee('Rice Bowl Teriyaki');

        // Requirement: "badge khusus untuk menu exclusive cabang (rice bowl hanya tersedia di MERR)"
        // Cek pada daftar-menu apakah produk dengan id_cabang memiliki badge khusus cabang
        // Di view daftar-menu.blade.php, elemen kartu menu HANYA me-render:
        // .product-art, .product-name, .price, .btn
        // TIDAK ADA badge eksklusif cabang sama sekali!
        $content = $response->getContent();
        $this->assertStringNotContainsString('Khusus MERR', $content);
        $this->assertStringNotContainsString('Eksklusif Cabang', $content);
        $this->assertStringNotContainsString('Hanya di MERR', $content);
    }

    public function test_katalog_menu_tidak_menampilkan_info_kalori(): void
    {
        $response = $this->get(route('customer.daftar_menu'));
        $response->assertStatus(200);
        $response->assertDontSee('kkal');
        $response->assertDontSee('kalori');
        $response->assertDontSee('calorie');
    }

    // ==========================================
    // B. CUSTOMER: KREASIKAN SALAD SENDIRI
    // ==========================================

    public function test_kreasikan_salad_hanya_menampilkan_produk_bisa_dikustom(): void
    {
        $response = $this->get(route('customer.kreasikan_salad'));
        $response->assertStatus(200);
        // Salad Sayur Segar (bisa_dikustom = true) HARUS muncul
        $response->assertSee('Salad Sayur Segar');
        // Chef's Salad (bisa_dikustom = false) TIDAK BOLEH muncul
        $response->assertDontSee("Chef's Salad Special");
    }

    public function test_kreasikan_salad_menampilkan_sayuran_dan_protein(): void
    {
        $response = $this->get(route('customer.kreasikan_salad'));
        $response->assertStatus(200);
        $response->assertSee('Selada Hijau');
        $response->assertSee('Tomat Segar');
        $response->assertSee('Chicken Patties');
    }

    // ==========================================
    // C. CUSTOMER: CHECKOUT
    // ==========================================

    public function test_checkout_validation_gagal_jika_cart_kosong(): void
    {
        $response = $this->postJson(route('customer.checkout.store'), [
            'nama_pelanggan' => 'Budi Santoso',
            'no_whatsapp' => '081234567890',
            'metode_ambil' => 'pickup',
            'metode_bayar' => 'tunai',
            'cart' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cart']);
    }

    public function test_checkout_validation_gagal_jika_delivery_tanpa_alamat(): void
    {
        $response = $this->postJson(route('customer.checkout.store'), [
            'nama_pelanggan' => 'Budi Santoso',
            'no_whatsapp' => '081234567890',
            'metode_ambil' => 'delivery',
            'alamat_delivery' => null,
            'metode_bayar' => 'tunai',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->saladSayur->id_produk,
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['alamat_delivery']);
    }

    public function test_checkout_happy_path_pickup_tunai(): void
    {
        $response = $this->postJson(route('customer.checkout.store'), [
            'nama_pelanggan' => 'Siti Nurhaliza',
            'no_whatsapp' => '081298765432',
            'metode_ambil' => 'pickup',
            'metode_bayar' => 'tunai',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->saladSayur->id_produk,
                    'jumlah' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $json = $response->json();
        $this->assertNotEmpty($json['kode_pesanan']);
        // 2 * harga_online (25000) = 50000
        $this->assertEquals(50000, $json['total_bayar']);

        // Verifikasi ke database
        $this->assertDatabaseHas('pesanan', [
            'kode_pesanan' => $json['kode_pesanan'],
            'jenis' => 'online',
            'metode_ambil' => 'pickup',
            'subtotal' => 50000,
            'total_bayar' => 50000,
            'ongkir' => 0,
            'status_ongkir' => 'tidak_perlu',
        ]);
    }

    public function test_checkout_custom_salad_tanpa_protein_tetap_berhasil(): void
    {
        // Requirement: protein bersifat opsional (boleh 0 protein, hanya sayuran)
        $response = $this->postJson(route('customer.checkout.store'), [
            'nama_pelanggan' => 'Anisa Diet',
            'no_whatsapp' => '081333444555',
            'metode_ambil' => 'pickup',
            'metode_bayar' => 'tunai',
            'cart' => [
                [
                    'tipe' => 'custom',
                    'id_produk' => $this->saladSayur->id_produk,
                    'id_komponen' => [$this->kompSelada->id_komponen, $this->kompTomat->id_komponen], // HANYA SAYURAN
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $json = $response->json();
        // harga dasar salad sayur online: 25000 + 0 (komponen gratis) = 25000
        $this->assertEquals(25000, $json['total_bayar']);

        $pesanan = Pesanan::where('kode_pesanan', $json['kode_pesanan'])->first();
        $this->assertNotNull($pesanan);
        $detail = $pesanan->detailPesanan->first();
        $this->assertEquals('Kreasikan Salad Sendiri', $detail->nama_item);
        $this->assertNull($detail->id_produk);
        $this->assertCount(2, $detail->komponenSalad);
    }

    public function test_checkout_anti_tamper_harga_dari_frontend(): void
    {
        // User mencoba mengirim harga Rp 100 via frontend payload
        $response = $this->postJson(route('customer.checkout.store'), [
            'nama_pelanggan' => 'Hacker Tamper',
            'no_whatsapp' => '081999888777',
            'metode_ambil' => 'pickup',
            'metode_bayar' => 'tunai',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->saladSayur->id_produk,
                    'harga' => 100, // DI-TAMPER
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $json = $response->json();
        // Backend WAJIB menghitung ulang menggunakan harga master di DB (25000), bukan 100
        $this->assertEquals(25000, $json['total_bayar']);
    }

    public function test_checkout_delivery_ongkir_dan_status_ongkir_temuan(): void
    {
        // Requirement PRD: "untuk delivery ongkir berstatus 'menunggu konfirmasi admin' (bukan auto-hitung)"
        // Implementasi aktual: auto hitung via Pengaturan::getTarifOngkir() dan status_ongkir='terkonfirmasi'
        $response = $this->postJson(route('customer.checkout.store'), [
            'nama_pelanggan' => 'Rina Delivery',
            'no_whatsapp' => '081234111222',
            'metode_ambil' => 'delivery',
            'alamat_delivery' => 'Jl. Ketintang Baru No. 5, Surabaya',
            'metode_bayar' => 'tunai',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->saladSayur->id_produk,
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $json = $response->json();

        $pesanan = Pesanan::where('kode_pesanan', $json['kode_pesanan'])->first();
        // Catatan pengujian: Cek apakah sistem menerapkan auto-hitung ongkir
        $this->assertEquals(5000, $pesanan->ongkir);
        $this->assertEquals('terkonfirmasi', $pesanan->status_ongkir);
    }

    public function test_promo_stacking_pada_checkout(): void
    {
        // Buat 2 promo aktif yang keduanya memenuhi syarat min porsi >= 1
        $promo1 = Promo::create([
            'nama_promo' => 'Diskon Pembukaan 5rb',
            'tipe_potongan' => 'nominal',
            'nilai_potongan' => 5000,
            'syarat_min_porsi' => 1,
            'batas_penggunaan' => 10,
            'jumlah_terpakai' => 0,
            'status_aktif' => true,
        ]);

        $promo2 = Promo::create([
            'nama_promo' => 'Diskon Sehat 3rb',
            'tipe_potongan' => 'nominal',
            'nilai_potongan' => 3000,
            'syarat_min_porsi' => 1,
            'batas_penggunaan' => 10,
            'jumlah_terpakai' => 0,
            'status_aktif' => true,
        ]);

        $response = $this->postJson(route('customer.checkout.store'), [
            'nama_pelanggan' => 'Promo Hunter',
            'no_whatsapp' => '081555666777',
            'metode_ambil' => 'pickup',
            'metode_bayar' => 'tunai',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->saladSayur->id_produk,
                    'jumlah' => 1, // Subtotal = 25000
                ],
            ],
        ]);

        $response->assertStatus(201);
        $json = $response->json();

        // Subtotal = 25.000, Promo 1 (5.000) + Promo 2 (3.000) = Total Diskon 8.000 (Stacking)
        // Total Bayar = 25.000 - 8.000 = 17.000
        $this->assertEquals(8000, $json['total_diskon']);
        $this->assertEquals(17000, $json['total_bayar']);

        // Verifikasi relasi pivot pesanan_promo menyimpan kedua promo
        $pesanan = Pesanan::where('kode_pesanan', $json['kode_pesanan'])->first();
        $this->assertCount(2, $pesanan->promo);
    }

    // ==========================================
    // D. CUSTOMER: STATUS PESANAN & RATING
    // ==========================================

    public function test_status_pesanan_kode_invalid(): void
    {
        $response = $this->get(route('customer.status_pesanan', ['kode' => 'KODE-PALSU-999']));
        $response->assertStatus(200);
        // Halaman status pesanan tidak menampilkan pesan error 'tidak ditemukan',
        // melainkan hanya kembali menampilkan form input lacak 'Cari Pesanan Anda'
        $response->assertSee('Cari Pesanan Anda');
        $this->assertStringNotContainsString('tidak ditemukan', strtolower($response->getContent()));
    }

    public function test_api_status_pesanan_live(): void
    {
        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-TEST-001',
            'jenis' => 'online',
            'id_cabang' => $this->cabangLidah->id_cabang,
            'metode_ambil' => 'pickup',
            'subtotal' => 25000,
            'total_bayar' => 25000,
            'metode_bayar' => 'tunai',
            'status_pesanan' => 'diproses',
            'status_pembayaran' => 'menunggu_verifikasi',
        ]);

        $response = $this->getJson(route('customer.pesanan.api_status', ['kode' => 'HMT-TEST-001']));
        $response->assertStatus(200);
        $response->assertJson([
            'ok' => true,
            'kode_pesanan' => 'HMT-TEST-001',
            'status_pesanan' => 'diproses',
            'total_bayar' => 25000,
        ]);
    }

    public function test_fitur_rating_belum_memiliki_route(): void
    {
        // Sesuai PRD F7: Customer bisa memberi rating 1-5 bintang per pesanan
        // Di routes/web.php sama sekali belum ada route rating
        $hasRatingRoute = Route::has('customer.rating') ||
                          Route::has('rating.store');
        $this->assertFalse($hasRatingRoute, 'FAKTA TEMUAN: Route rating belum terdaftar di web.php');
    }

    // ==========================================
    // E. ADMIN: AUTH, DASHBOARD, & MENU
    // ==========================================

    public function test_admin_dashboard_terproteksi_middleware(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_dashboard_bisa_diakses_setelah_login(): void
    {
        $response = $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Total Pesanan');
        $response->assertSee('Grafik Penjualan 7 Hari Terakhir');
    }

    public function test_admin_kelola_menu_toggle_sold_out(): void
    {
        $this->assertTrue((bool) $this->saladSayur->status_aktif);

        $response = $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->patch(route('admin.kelola_menu.status', ['id' => $this->saladSayur->id_produk]));

        $response->assertRedirect();
        $this->saladSayur->refresh();
        $this->assertFalse((bool) $this->saladSayur->status_aktif);
    }

    public function test_admin_kelola_menu_tidak_ada_crud_komponen_salad(): void
    {
        // Requirement PRD A2: Mengatur seluruh menu reguler dan komponen "Kreasikan Salad" (sayuran, protein)
        // Di MenuController tidak ada method atau route untuk komponen salad
        $hasKomponenRoute = Route::has('admin.kelola_komponen') ||
                            Route::has('admin.komponen.index');
        $this->assertFalse($hasKomponenRoute, 'FAKTA TEMUAN: Route pengelolaan komponen salad belum ada di web.php');
    }

    // ==========================================
    // F. ADMIN: KASIR
    // ==========================================

    public function test_admin_kasir_halaman_bisa_diakses_dan_menampilkan_cabang_merr(): void
    {
        $response = $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->get(route('admin.kasir'));

        $response->assertStatus(200);
        $response->assertSee('Cabang Outlet');
        $response->assertSee('MERR');
    }

    public function test_admin_kasir_input_transaksi_offline_harga_offline(): void
    {
        // Harga offline Salad Sayur adalah 20.000 (bukan harga online 25.000)
        $response = $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->post(route('admin.kasir.store'), [
            'id_cabang' => $this->cabangMerr->id_cabang,
            'nama_pelanggan' => 'Walk-in Pelanggan',
            'metode_bayar' => 'tunai',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->saladSayur->id_produk,
                    'jumlah' => 2,
                ],
            ],
        ]);

        $response->assertRedirect();
        // 2 * 20.000 = 40.000
        $this->assertDatabaseHas('pesanan', [
            'jenis' => 'kasir',
            'id_cabang' => $this->cabangMerr->id_cabang,
            'subtotal' => 40000,
            'total_bayar' => 40000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'terverifikasi',
            'id_admin' => $this->admin->id_admin,
        ]);
    }

    public function test_admin_kasir_input_transaksi_offline_qris(): void
    {
        $response = $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->post(route('admin.kasir.store'), [
            'id_cabang' => $this->cabangMerr->id_cabang,
            'nama_pelanggan' => 'QRIS Walk-in',
            'metode_bayar' => 'qris',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->saladSayur->id_produk,
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pesanan', [
            'jenis' => 'kasir',
            'id_cabang' => $this->cabangMerr->id_cabang,
            'metode_bayar' => 'qris',
            'status_pembayaran' => 'terverifikasi',
            'id_admin' => $this->admin->id_admin,
        ]);
    }

    public function test_admin_kasir_input_transaksi_otomatis_cabang_merr_jika_tanpa_id_cabang(): void
    {
        $response = $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->post(route('admin.kasir.store'), [
            // id_cabang sengaja tidak dikirimkan
            'nama_pelanggan' => 'Auto Merr Walk-in',
            'metode_bayar' => 'tunai',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->saladSayur->id_produk,
                    'jumlah' => 1,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pesanan', [
            'jenis' => 'kasir',
            'id_cabang' => $this->cabangMerr->id_cabang,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'terverifikasi',
            'id_admin' => $this->admin->id_admin,
        ]);
    }

    // ==========================================
    // G. ADMIN: DAFTAR PESANAN & BATALKAN ITEM
    // ==========================================

    public function test_admin_batalkan_item_jika_stok_habis(): void
    {
        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-BATAL-01',
            'jenis' => 'online',
            'id_cabang' => $this->cabangLidah->id_cabang,
            'metode_ambil' => 'pickup',
            'subtotal' => 50000,
            'total_bayar' => 50000,
            'metode_bayar' => 'tunai',
            'status_pesanan' => 'diproses',
            'status_pembayaran' => 'terverifikasi',
        ]);

        $item1 = DetailPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_produk' => $this->saladSayur->id_produk,
            'nama_item' => 'Salad Sayur Segar',
            'jumlah' => 1,
            'harga_satuan' => 25000,
            'subtotal_item' => 25000,
            'status_item' => 'aktif',
        ]);

        $item2 = DetailPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_produk' => $this->chefsSalad->id_produk,
            'nama_item' => "Chef's Salad Special",
            'jumlah' => 1,
            'harga_satuan' => 25000,
            'subtotal_item' => 25000,
            'status_item' => 'aktif',
        ]);

        // Admin membatalkan item1 karena stok habis
        $response = $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->patch(route('admin.daftar_pesanan.item.batal', ['id' => $item1->id_detail]));

        $response->assertRedirect();
        $item1->refresh();
        $this->assertEquals('dibatalkan', $item1->status_item);

        // Subtotal pesanan induk berkurang dari 50.000 menjadi 25.000
        $pesanan->refresh();
        $this->assertEquals(25000, $pesanan->subtotal);
        $this->assertEquals(25000, $pesanan->total_bayar);
    }

    public function test_admin_update_status_pesanan_ke_siap_dan_selesai(): void
    {
        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-STATUS-01',
            'jenis' => 'online',
            'id_cabang' => $this->cabangLidah->id_cabang,
            'metode_ambil' => 'pickup',
            'subtotal' => 25000,
            'total_bayar' => 25000,
            'metode_bayar' => 'tunai',
            'status_pesanan' => 'diproses',
        ]);

        $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->patch(route('admin.daftar_pesanan.status', ['id' => $pesanan->id_pesanan]), [
            'status_pesanan' => 'siap',
        ]);

        $pesanan->refresh();
        $this->assertEquals('siap', $pesanan->status_pesanan);

        $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->patch(route('admin.daftar_pesanan.status', ['id' => $pesanan->id_pesanan]), [
            'status_pesanan' => 'selesai',
        ]);

        $pesanan->refresh();
        $this->assertEquals('selesai', $pesanan->status_pesanan);
    }

    // ==========================================
    // H. ADMIN: PROMO, LAPORAN, PENGATURAN
    // ==========================================

    public function test_admin_promo_auto_nonaktif_saat_kuota_tercapai(): void
    {
        $promo = Promo::create([
            'nama_promo' => 'Promo Terbatas',
            'tipe_potongan' => 'nominal',
            'nilai_potongan' => 5000,
            'batas_penggunaan' => 1,
            'jumlah_terpakai' => 0,
            'status_aktif' => true,
        ]);

        // Gunakan promo lewat checkout
        $this->postJson(route('customer.checkout.store'), [
            'nama_pelanggan' => 'User Kuota',
            'no_whatsapp' => '081234567899',
            'metode_ambil' => 'pickup',
            'metode_bayar' => 'tunai',
            'cart' => [
                [
                    'tipe' => 'produk',
                    'id_produk' => $this->saladSayur->id_produk,
                    'jumlah' => 1,
                ],
            ],
        ]);

        $promo->refresh();
        $this->assertEquals(1, $promo->jumlah_terpakai);
        $this->assertFalse((bool) $promo->status_aktif);
    }

    public function test_admin_laporan_agregasi_penjualan_per_item(): void
    {
        $pesanan = Pesanan::create([
            'kode_pesanan' => 'HMT-LAP-01',
            'jenis' => 'online',
            'id_cabang' => $this->cabangLidah->id_cabang,
            'metode_ambil' => 'pickup',
            'subtotal' => 50000,
            'total_bayar' => 50000,
            'metode_bayar' => 'tunai',
            'status_pembayaran' => 'terverifikasi',
        ]);

        DetailPesanan::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'id_produk' => $this->saladSayur->id_produk,
            'nama_item' => 'Salad Sayur Segar',
            'jumlah' => 2,
            'harga_satuan' => 25000,
            'subtotal_item' => 50000,
            'status_item' => 'aktif',
        ]);

        $response = $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->get(route('admin.laporan'));

        $response->assertStatus(200);
        $response->assertSee('Salad Sayur Segar');
        $response->assertSee('50.000');
    }

    public function test_admin_pengaturan_update_jam_cabang(): void
    {
        $response = $this->withSession([
            'admin_id' => $this->admin->id_admin,
            'admin_nama' => $this->admin->nama,
        ])->patch(route('admin.pengaturan.cabang.update', ['cabang' => $this->cabangMerr->id_cabang]), [
            'nama_cabang' => 'Hamta MERR (Updated)',
            'alamat' => 'Apartemen Bale Hinggil Lt. Ground Blok A',
            'jam_buka' => '10:00',
            'jam_tutup' => '22:00',
            'layani_pickup' => true,
            'layani_dinein' => true,
        ]);

        $response->assertRedirect();
        $this->cabangMerr->refresh();
        $this->assertEquals('10:00', $this->cabangMerr->jam_buka);
        $this->assertEquals('22:00', $this->cabangMerr->jam_tutup);
        $this->assertEquals('Hamta MERR (Updated)', $this->cabangMerr->nama_cabang);
    }
}
