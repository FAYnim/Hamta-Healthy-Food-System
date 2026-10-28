<?php

namespace Tests\Feature;

use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelolaMenuStatusTest extends TestCase
{
    use RefreshDatabase;

    private function produk(bool $aktif = true): Produk
    {
        return Produk::create([
            'nama_produk' => 'Salad Uji',
            'kategori' => 'Salad',
            'harga_offline' => 15000,
            'harga_online' => 18000,
            'status_aktif' => $aktif,
        ]);
    }

    private function admin()
    {
        return $this->withSession(['admin_id' => 1, 'admin_nama' => 'Uji']);
    }

    public function test_toggle_menandai_sold_out(): void
    {
        $produk = $this->produk();
        $this->assertTrue($produk->status_aktif);

        $this->admin()
            ->from(route('admin.kelola_menu', ['kategori' => 'Salad']))
            ->patch(route('admin.kelola_menu.status', $produk->id_produk))
            ->assertRedirect(route('admin.kelola_menu', ['kategori' => 'Salad']))
            ->assertSessionHas('success');

        $this->assertFalse($produk->fresh()->status_aktif);
    }

    public function test_hapus_mempertahankan_filter(): void
    {
        $produk = $this->produk();

        $this->admin()
            ->from(route('admin.kelola_menu', ['cari' => 'Salad']))
            ->delete(route('admin.kelola_menu.destroy', $produk->id_produk))
            ->assertRedirect(route('admin.kelola_menu', ['cari' => 'Salad']))
            ->assertSessionHas('success');

        $this->assertNull($produk->fresh());
    }

    public function test_toggle_menolak_redirect_ke_host_lain(): void
    {
        $produk = $this->produk();

        $this->admin()
            ->withHeader('Referer', 'https://evil.example/pancing')
            ->patch(route('admin.kelola_menu.status', $produk->id_produk))
            ->assertRedirect(route('admin.kelola_menu'));

        $this->assertFalse($produk->fresh()->status_aktif);
    }

    public function test_hapus_menolak_redirect_ke_host_lain(): void
    {
        $produk = $this->produk();

        $this->admin()
            ->withHeader('Referer', 'https://evil.example/pancing')
            ->delete(route('admin.kelola_menu.destroy', $produk->id_produk))
            ->assertRedirect(route('admin.kelola_menu'));

        $this->assertNull($produk->fresh());
    }

    public function test_toggle_menolak_referer_protocol_relative(): void
    {
        $produk = $this->produk();

        $this->admin()
            ->withHeader('Referer', '//evil.example/x')
            ->patch(route('admin.kelola_menu.status', $produk->id_produk))
            ->assertRedirect(route('admin.kelola_menu'));
    }

    public function test_toggle_mengaktifkan_kembali(): void
    {
        $produk = $this->produk(false);

        $this->admin()
            ->patch(route('admin.kelola_menu.status', $produk->id_produk));

        $this->assertTrue($produk->fresh()->status_aktif);
    }

    public function test_tabel_menampilkan_badge_status(): void
    {
        $this->produk(true);
        $this->produk(false);

        $this->admin()
            ->get(route('admin.kelola_menu'))
            ->assertOk()
            ->assertSee('Tersedia')
            ->assertSee('Sold-out');
    }

    public function test_tanpa_login_tidak_bisa_toggle(): void
    {
        $produk = $this->produk();

        $this->patch(route('admin.kelola_menu.status', $produk->id_produk))
            ->assertRedirect(route('admin.login'));

        $this->assertTrue($produk->fresh()->status_aktif);
    }
}
