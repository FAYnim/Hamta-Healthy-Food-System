<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_menampilkan_widget_recaptcha(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertSee('https://www.google.com/recaptcha/api.js');
        $response->assertSee('g-recaptcha');
        $response->assertSee(config('services.recaptcha.site_key'));
    }

    public function test_login_membutuhkan_recaptcha(): void
    {
        $admin = Admin::create([
            'username' => 'admin_test',
            'password' => Hash::make('rahasia123'),
            'nama' => 'Admin Test',
        ]);

        $response = $this->post(route('admin.login'), [
            'username' => 'admin_test',
            'password' => 'rahasia123',
            // tanpa g-recaptcha-response
        ]);

        $response->assertSessionHasErrors('g-recaptcha-response');
        $this->assertGuest();
    }

    public function test_login_gagal_jika_verifikasi_recaptcha_ditolak_google(): void
    {
        Admin::create([
            'username' => 'admin_test',
            'password' => Hash::make('rahasia123'),
            'nama' => 'Admin Test',
        ]);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify*' => Http::response([
                'success' => false,
                'error-codes' => ['invalid-input-response'],
            ], 200),
        ]);

        $response = $this->post(route('admin.login'), [
            'username' => 'admin_test',
            'password' => 'rahasia123',
            'g-recaptcha-response' => 'token_palsu',
        ]);

        $response->assertSessionHas('error', 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.');
        $this->assertNull(session('admin_id'));
    }

    public function test_login_berhasil_dengan_recaptcha_valid_dan_kredensial_benar(): void
    {
        $admin = Admin::create([
            'username' => 'admin_test',
            'password' => Hash::make('rahasia123'),
            'nama' => 'Admin Test',
        ]);

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify*' => Http::response([
                'success' => true,
                'challenge_ts' => now()->toIso8601String(),
                'hostname' => 'localhost',
            ], 200),
        ]);

        $response = $this->post(route('admin.login'), [
            'username' => 'admin_test',
            'password' => 'rahasia123',
            'g-recaptcha-response' => 'token_valid',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertEquals($admin->id_admin, session('admin_id'));
        $this->assertEquals('Admin Test', session('admin_nama'));
    }
}
