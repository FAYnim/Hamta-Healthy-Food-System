<?php

namespace Tests\Feature;

use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TempReviewProbeTest extends TestCase
{
    use RefreshDatabase;

    private function admin()
    {
        return $this->withSession(['admin_id' => 1, 'admin_nama' => 'Uji']);
    }

    private function produk(string $nama): Produk
    {
        return Produk::create([
            'nama_produk' => $nama,
            'kategori' => 'Salad',
            'harga_offline' => 15000,
            'harga_online' => 18000,
            'status_aktif' => true,
        ]);
    }

    public function test_probe_session_previous_no_referer(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->produk(sprintf('Menu %02d', $i));
        }
        $target = Produk::orderBy('nama_produk')->skip(10)->first();

        // simulate real flow: GET filtered page 2 (stores session previous URL)
        $this->admin()->get(route('admin.kelola_menu', ['kategori' => 'Salad', 'cari' => 'Menu', 'page' => 2]))
            ->assertOk();

        $r = $this->admin()->patch(route('admin.kelola_menu.status', $target->id_produk));
        fwrite(STDERR, "\nPROBE no-referer with session previous -> " . $r->headers->get('Location') . "\n");

        $this->assertTrue(true);
    }

    public function test_probe_case_and_port_referer(): void
    {
        $p = $this->produk('X');
        foreach ([
            'http://LOCALHOST/admin/kelola-menu?page=2',
            'http://localhost:9999/admin/kelola-menu?page=2',
            'http://[::1]:8000/admin/kelola-menu?page=2',
            'http://localhost./admin/kelola-menu?page=2',
        ] as $ref) {
            $r = $this->admin()->withHeader('Referer', $ref)->patch(route('admin.kelola_menu.status', $p->id_produk));
            fwrite(STDERR, "\nPROBE referer={$ref} -> " . $r->headers->get('Location') . "\n");
            $p->refresh();
        }
        $this->assertTrue(true);
    }
}
