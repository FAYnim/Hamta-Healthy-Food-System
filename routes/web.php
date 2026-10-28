<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KasirController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\PromoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ─── Auth (tidak perlu middleware) ────────────────────────────────────────────
Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'login']);
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ─── Admin (dilindungi middleware) ────────────────────────────────────────────
Route::middleware('admin.auth')->group(function () {

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/kasir', [KasirController::class, 'index'])->name('admin.kasir');
    Route::post('/admin/kasir', [KasirController::class, 'store'])->name('admin.kasir.store');

    Route::get('/admin/promo', [PromoController::class, 'index'])
        ->name('admin.promo.index');
    Route::post('/admin/promo', [PromoController::class, 'store'])
        ->name('admin.promo.store');
    Route::match(['put', 'patch'], '/admin/promo/{id}', [PromoController::class, 'update'])
        ->name('admin.promo.update');
    Route::delete('/admin/promo/{id}', [PromoController::class, 'destroy'])
        ->name('admin.promo.destroy');
    Route::patch('/admin/promo/{id}/status', [PromoController::class, 'toggleStatus'])
        ->name('admin.promo.status');

    Route::get('/admin/daftar-pesanan', [PesananController::class, 'index'])
        ->name('admin.daftar_pesanan');
    Route::get('/admin/daftar-pesanan/{id}', [PesananController::class, 'show'])
        ->name('admin.daftar_pesanan.show');
    Route::patch('/admin/daftar-pesanan/{id}/status', [PesananController::class, 'updateStatus'])
        ->name('admin.daftar_pesanan.status');
    Route::patch('/admin/daftar-pesanan/{id}/ongkir', [PesananController::class, 'konfirmasiOngkir'])
        ->name('admin.daftar_pesanan.ongkir');
    Route::patch('/admin/daftar-pesanan/item/{id}/batal', [PesananController::class, 'batalkanItem'])
        ->name('admin.daftar_pesanan.item.batal');

    Route::get('/admin/pengaturan', [PengaturanController::class, 'index'])
        ->name('admin.pengaturan');
    Route::post('/admin/pengaturan/identitas', [PengaturanController::class, 'updateIdentitas'])
        ->name('admin.pengaturan.identitas');
    Route::post('/admin/pengaturan/kontak', [PengaturanController::class, 'updateKontak'])
        ->name('admin.pengaturan.kontak');
    Route::post('/admin/pengaturan/cabang', [PengaturanController::class, 'storeCabang'])
        ->name('admin.pengaturan.cabang.store');
    Route::patch('/admin/pengaturan/cabang/{cabang}', [PengaturanController::class, 'updateCabang'])
        ->name('admin.pengaturan.cabang.update');
    Route::delete('/admin/pengaturan/cabang/{cabang}', [PengaturanController::class, 'destroyCabang'])
        ->name('admin.pengaturan.cabang.destroy');

    Route::get('/admin/kelola-menu', [MenuController::class, 'index'])
        ->name('admin.kelola_menu');
    Route::post('/admin/kelola-menu', [MenuController::class, 'store'])
        ->name('admin.kelola_menu.store');
    Route::patch('/admin/kelola-menu/{id}/status', [MenuController::class, 'toggleStatus'])
        ->name('admin.kelola_menu.status');
    Route::patch('/admin/kelola-menu/{id}', [MenuController::class, 'update'])
        ->name('admin.kelola_menu.update');
    Route::delete('/admin/kelola-menu/{id}', [MenuController::class, 'destroy'])
        ->name('admin.kelola_menu.destroy');

    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan');

});
