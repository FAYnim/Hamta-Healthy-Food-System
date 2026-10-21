<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;


// Extends base Controller Laravel (App\Http\Controllers\Controller),
// karena class App\Http\Controllers\Admin\Controller belum dibuat
class AuthController extends \App\Http\Controllers\Controller
{
    public function showLoginForm() {
        // Tampilkan form login admin
        return view('admin.login');
    }

    public function login(Request $request) {
        // Validasi input form login
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        // Cari admin berdasarkan username di tabel admin (Eloquent, bukan raw query)
        $admin = Admin::where('username', $data['username'])->first();

        // Password dicek manual pakai Hash::check, BUKAN Auth::attempt() bawaan Laravel.
        // Auth::attempt() memakai provider 'users' bawaan (tabel users + primary key id),
        // sedangkan tabel admin ini custom (primary key id_admin) dan tidak pakai guard bawaan.
        // Dicek manual supaya satu error message generik tidak membocorkan username mana yang terdaftar.
        if (!$admin || !Hash::check($data['password'], $admin->password)) {
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
            ->with('success', 'Selamat datang, ' . $admin->nama . '!');
    }

    public function logout(Request $request) {
        // Hapus session admin lalu arahkan kembali ke halaman login
        $request->session()->forget(['admin_id', 'admin_nama']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Anda sudah keluar dari akun admin.');
    }
}
