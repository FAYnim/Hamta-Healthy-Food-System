<?php

namespace Tests\Feature;

use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaMenuFilterTest extends TestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return $this->withSession(['admin_id' => 1, 'admin_nama' => 'Uji']);
    }

    private function produk(string $nama, string $kategori = 'Salad'): Produk
    {
        return Produk::create([
            'nama_produk' => $nama,
            'kategori' => $kategori,
            'harga_offline' => 15000,
            'harga_online' => 18000,
            'status_aktif' => true,
        ]);
    }

    public function test_filter_kategori_hanya_menampilkan_kategori_itu(): void
    {
        $this->produk('Salad Uji', 'Salad');
        $this->produk('Sandwich Ayam', 'Sandwich');

        $this->admin()
            ->get(route('admin.kelola_menu', ['kategori' => 'Salad']))
            ->assertOk()
            ->assertSee('Salad Uji')
            ->assertDontSee('Sandwich Ayam');
    }

    public function test_pencarian_bersifat_substring(): void
    {
        $this->produk('Salad Uji', 'Salad');
        $this->produk('Sandwich Ayam', 'Sandwich');

        $this->admin()
            ->get(route('admin.kelola_menu', ['cari' => 'ayam']))
            ->assertOk()
            ->assertSee('Sandwich Ayam')
            ->assertDontSee('Salad Uji');
    }

    public function test_filter_dan_pencarian_digabung(): void
    {
        $this->produk('Juice Ayam', 'Juice');
        $this->produk('Juice Jeruk', 'Juice');
        $this->produk('Sandwich Ayam', 'Sandwich');

        $this->admin()
            ->get(route('admin.kelola_menu', ['kategori' => 'Juice', 'cari' => 'ayam']))
            ->assertOk()
            ->assertSee('Juice Ayam')
            ->assertDontSee('Juice Jeruk')
            ->assertDontSee('Sandwich Ayam');
    }

    public function test_pencarian_karakter_nol_tetap_menyaring(): void
    {
        $this->produk('Menu 0', 'Salad');
        $this->produk('Menu 1', 'Salad');

        $this->admin()
            ->get(route('admin.kelola_menu', ['cari' => '0']))
            ->assertOk()
            ->assertSee('Menu 0')
            ->assertDontSee('Menu 1');
    }

    public function test_kategori_tidak_valid_diabaikan(): void
    {
        $this->produk('Salad Uji', 'Salad');

        $this->admin()
            ->get(route('admin.kelola_menu', ['kategori' => 'Soup']))
            ->assertOk()
            ->assertSee('Salad Uji');
    }

    public function test_cari_berupa_array_tidak_error(): void
    {
        $this->produk('Salad Uji', 'Salad');

        $this->admin()
            ->get(route('admin.kelola_menu', ['cari' => ['x']]))
            ->assertOk()
            ->assertSee('Salad Uji');
    }

    public function test_dibatasi_sepuluh_baris_per_halaman(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->produk(sprintf('Menu %02d', $i), 'Salad');
        }

        $response = $this->admin()->get(route('admin.kelola_menu'));

        $response->assertOk();
        $this->assertSame(10, $response->viewData('produk')->count());
        $this->assertSame(12, $response->viewData('produk')->total());
    }

    public function test_halaman_kedua_membawa_query_string_filter(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->produk(sprintf('Menu %02d', $i), 'Salad');
        }

        $response = $this->admin()
            ->get(route('admin.kelola_menu', ['kategori' => 'Salad', 'cari' => 'Menu']));

        $response->assertOk();
        $this->assertStringContainsString(
            'kategori=Salad',
            $response->viewData('produk')->url(2)
        );
        $this->assertStringContainsString('cari=Menu', $response->viewData('produk')->url(2));
    }

    public function test_empty_state_filter_menampilkan_tombol_reset(): void
    {
        $this->produk('Salad Uji', 'Salad');

        $html = $this->admin()
            ->get(route('admin.kelola_menu', ['cari' => 'tidakada']))
            ->assertOk()
            ->assertSee('Tidak ada menu yang cocok dengan filter.')
            ->assertSee('Reset filter')
            ->getContent();

        // assertSee(route(...), false) tidak mendiskriminasi: href yang sama juga dipakai
        // link "Kelola Menu" di sidebar. Regex mengikat href ke label "Reset filter".
        $this->assertMatchesRegularExpression(
            '#<a href="' . preg_quote(route('admin.kelola_menu'), '#') . '"[^>]*>\s*Reset filter#s',
            $html
        );
    }

    public function test_empty_state_pencarian_nol_menampilkan_tombol_reset(): void
    {
        $this->produk('Menu 1', 'Salad');

        $this->admin()
            ->get(route('admin.kelola_menu', ['cari' => '0']))
            ->assertOk()
            ->assertSee('Tidak ada menu yang cocok dengan filter.')
            ->assertSee('Reset filter');
    }

    public function test_empty_state_tanpa_filter_menampilkan_pesan_dasar(): void
    {
        $this->admin()
            ->get(route('admin.kelola_menu'))
            ->assertOk()
            ->assertSee('Belum ada menu.')
            ->assertDontSee('Reset filter');
    }

    public function test_halaman_di_luar_jangkauan_tidak_bilang_belum_ada_menu(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->produk(sprintf('Menu %02d', $i), 'Salad');
        }

        $this->admin()
            ->get(route('admin.kelola_menu', ['page' => 5]))
            ->assertOk()
            ->assertSee('Halaman ini tidak memiliki menu.')
            ->assertDontSee('Belum ada menu.');
    }

    public function test_halaman_di_luar_jangkauan_dengan_filter_tetap_bilang_halaman_kosong(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->produk(sprintf('Menu %02d', $i), 'Salad');
        }

        $this->admin()
            ->get(route('admin.kelola_menu', ['kategori' => 'Salad', 'cari' => 'Menu', 'page' => 5]))
            ->assertOk()
            ->assertSee('Halaman ini tidak memiliki menu.')
            ->assertDontSee('Tidak ada menu yang cocok dengan filter.');
    }
}
