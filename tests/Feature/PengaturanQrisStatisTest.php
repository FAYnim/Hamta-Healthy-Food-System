<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengaturanQrisStatisTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_qris_statis_returns_null_when_no_record(): void
    {
        $this->assertNull(Pengaturan::getQrisStatis());
    }

    public function test_admin_can_upload_qris_statis_image(): void
    {
        Storage::fake('public');

        $admin = Admin::create([
            'nama' => 'Admin Test',
            'username' => 'admin_test',
            'password' => bcrypt('password123'),
        ]);

        session(['admin_id' => $admin->id_admin]);

        $file = UploadedFile::fake()->image('qris_barcode.png', 500, 500);

        $response = $this->post(route('admin.pengaturan.qris'), [
            'qris_statis' => $file,
        ]);

        $response->assertRedirect(route('admin.pengaturan'));
        $response->assertSessionHas('success', 'QRIS statis berhasil diperbarui.');

        $pengaturan = Pengaturan::first();
        $this->assertNotNull($pengaturan->qris_statis);
        Storage::disk('public')->assertExists($pengaturan->qris_statis);
        $this->assertEquals($pengaturan->qris_statis, Pengaturan::getQrisStatis());
    }

    public function test_admin_can_delete_qris_statis_image(): void
    {
        Storage::fake('public');

        $admin = Admin::create([
            'nama' => 'Admin Test',
            'username' => 'admin_test',
            'password' => bcrypt('password123'),
        ]);

        session(['admin_id' => $admin->id_admin]);

        $file = UploadedFile::fake()->image('qris_barcode.png', 500, 500);
        $path = $file->store('pengaturan', 'public');

        Pengaturan::create([
            'qris_statis' => $path,
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->post(route('admin.pengaturan.qris'), [
            'hapus_qris' => 1,
        ]);

        $response->assertRedirect(route('admin.pengaturan'));
        $response->assertSessionHas('success', 'QRIS statis berhasil dihapus.');

        $pengaturan = Pengaturan::first();
        $this->assertNull($pengaturan->qris_statis);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_checkout_page_renders_with_qris_statis_data(): void
    {
        Pengaturan::create([
            'qris_statis' => 'pengaturan/sample_qris.png',
        ]);

        $response = $this->get(route('customer.checkout'));

        $response->assertStatus(200);
        $response->assertViewHas('qrisStatis', 'pengaturan/sample_qris.png');
    }
}
