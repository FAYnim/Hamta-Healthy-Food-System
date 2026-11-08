<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Tampilkan form login admin
        return view('admin.login');
    }

    public function login(Request $request)
    {
        // Validasi input form login termasuk reCAPTCHA v2
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => ['required', 'string'],
        ], [
            'username.required' => 'Nama pengguna wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'g-recaptcha-response.required' => 'Silakan centang reCAPTCHA terlebih dahulu.',
        ]);

        // Verifikasi token reCAPTCHA v2 ke Google API
        $secretKey = config('services.recaptcha.secret_key') ?: env('RECAPTCHA_SECRET_KEY');
        if ($secretKey) {
            try {
                $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $data['g-recaptcha-response'],
                    'remoteip' => $request->ip(),
                ]);

                if (! $response->successful() || ! ($response->json('success') ?? false)) {
                    return redirect()->back()
                        ->withInput($request->only('username'))
                        ->with('error', 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.');
                }
            } catch (\Throwable $e) {
                Log::warning('reCAPTCHA verification warning: '.$e->getMessage());
                if (app()->isProduction()) {
                    return redirect()->back()
                        ->withInput($request->only('username'))
                        ->with('error', 'Layanan verifikasi reCAPTCHA tidak dapat dihubungi.');
                }
            }
        }

        // Cari admin berdasarkan username di tabel admin (Eloquent, bukan raw query)
        $admin = Admin::where('username', $data['username'])->first();

        // Password dicek manual pakai Hash::check, BUKAN Auth::attempt() bawaan Laravel.
        // Auth::attempt() memakai provider 'users' bawaan (tabel users + primary key id),
        // sedangkan tabel admin ini custom (primary key id_admin) dan tidak pakai guard bawaan.
        // Dicek manual supaya satu error message generik tidak membocorkan username mana yang terdaftar.
        if (! $admin || ! Hash::check($data['password'], $admin->password)) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->with('error', 'Username atau password salah.');
        }

        // Simpan data admin di session sebagai penanda sudah login
        $request->session()->regenerate();
        $request->session()->put([
            'admin_id' => $admin->id_admin,
            'admin_nama' => $admin->nama,
        ]);

        // Redirect admin ke dashboard
        return redirect()->route('admin.dashboard')
            ->with('success', 'Selamat datang, '.$admin->nama.'!');
    }

    public function logout(Request $request)
    {
        // Hapus session admin lalu arahkan kembali ke halaman login
        $request->session()->forget(['admin_id', 'admin_nama']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Anda sudah keluar dari akun admin.');
    }
}
