<?php

namespace Tests\Feature;

use App\Models\Admin;
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

    public function test_admin_can_update_tarif_ongkir(): void
    {
        $admin = Admin::create([
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
}
