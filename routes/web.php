<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// ─── Auth (tidak perlu middleware) ────────────────────────────────────────────
Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AuthController::class, 'login']);
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// ─── Admin (dilindungi middleware) ────────────────────────────────────────────
Route::middleware('admin.auth')->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/kasir', function () {
        return view('admin.kasir');
    });

    Route::get('/admin/promo', function () {
        return view('admin.promo');
    });

    Route::get('/admin/daftar-pesanan', function () {
        return view('admin.daftar_pesanan');
    });

    Route::get('/admin/pengaturan', function () {
        return view('admin.pengaturan');
    });

    Route::get('/admin/kelola-menu', function () {
        return view('admin.kelola_menu');
    });

});