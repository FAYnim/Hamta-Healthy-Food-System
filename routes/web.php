<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('admin.login');
});

Route::get('/admin/kasir', function () {
    return view('admin.kasir');
});

Route::get('/admin/promo', function () {
    return view('admin.promo');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
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