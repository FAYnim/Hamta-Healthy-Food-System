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
    })->name('admin.kasir');

    Route::get('/admin/promo', function () {
        return view('admin.promo');
    })->name('admin.promo');

    Route::get('/admin/daftar-pesanan', function () {
        return view('admin.daftar_pesanan');
    })->name('admin.daftar_pesanan');

    Route::get('/admin/pengaturan', function () {
        return view('admin.pengaturan');
    })->name('admin.pengaturan');

    Route::get('/admin/kelola-menu', [\App\Http\Controllers\Admin\MenuController::class, 'index'])
        ->name('admin.kelola_menu');
    Route::post('/admin/kelola-menu', [\App\Http\Controllers\Admin\MenuController::class, 'store'])
        ->name('admin.kelola_menu.store');
    Route::patch('/admin/kelola-menu/{id}/status', [\App\Http\Controllers\Admin\MenuController::class, 'toggleStatus'])
        ->name('admin.kelola_menu.status');
    Route::patch('/admin/kelola-menu/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])
        ->name('admin.kelola_menu.update');
    Route::delete('/admin/kelola-menu/{id}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])
        ->name('admin.kelola_menu.destroy');

});