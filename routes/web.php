<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KasirController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\StatusPesananController;
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\RatingController;
use App\Models\Cabang;
use App\Models\KomponenSalad;
use App\Models\Pengaturan;
use App\Models\Produk;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $menus = Produk::where('status_aktif', true)
        ->with(['cabang', 'bahan'])
        ->orderByDesc('id_produk')
        ->limit(8)
        ->get();

    return view('welcome', ['menus' => $menus]);
})->name('home');

// ─── Customer ────────────────────────────────────────────────────────────────
Route::get('/daftar-menu', function () {
    $bestSellers = Produk::where('status_aktif', true)
        ->with(['cabang', 'bahan'])
        ->withSum(['detailPesanan as terjual' => fn ($q) => $q->where('status_item', 'aktif')], 'jumlah')
        ->orderByDesc('terjual')
        ->orderByDesc('id_produk')
        ->limit(4)
        ->get();

    $menus = Produk::where('status_aktif', true)
        ->with(['cabang', 'bahan'])
        ->orderBy('nama_produk')
        ->get();

    return view('customer.daftar-menu', [
        'bestSellers' => $bestSellers,
        'menusByKategori' => $menus->groupBy('kategori'),
        'kategoriUrutan' => ['Salad', 'Sandwich', 'Juice'],
    ]);
})->name('customer.daftar_menu');

Route::get('/produk/{id}', function ($id) {
    $produk = Produk::with(['cabang', 'bahan'])->findOrFail($id);

    if (request()->wantsJson() || request()->ajax() || request()->header('Accept') === 'application/json') {
        return response()->json([
            'ok' => true,
            'id_produk' => $produk->id_produk,
            'nama_produk' => $produk->nama_produk,
            'kategori' => $produk->kategori,
            'deskripsi' => $produk->deskripsi,
            'harga_online' => (float) $produk->harga_online,
            'harga_offline' => (float) $produk->harga_offline,
            'foto' => $produk->foto ? asset('storage/'.$produk->foto) : null,
            'cabang' => $produk->cabang ? $produk->cabang->nama_cabang : null,
            'bahan' => $produk->bahan->map(fn ($b) => [
                'nama' => $b->nama_bahan,
                'keterangan' => $b->keterangan,
            ]),
        ]);
    }

    return redirect()->route('customer.daftar_menu', ['modal' => $produk->id_produk]);
})->name('customer.produk.detail');

Route::get('/detail-produk/{id}', function ($id) {
    return redirect()->route('customer.produk.detail', ['id' => $id]);
});

Route::get('/kreasikan-salad', function () {
    $base = Produk::where('status_aktif', true)
        ->where('bisa_dikustom', true)
        ->orderBy('nama_produk')
        ->get();
    $komponen = KomponenSalad::where('status_aktif', true)
        ->orderBy('nama_komponen')
        ->get();

    return view('customer.kreasikan-salad', [
        'baseOptions' => $base,
        'sayuran' => $komponen->where('jenis', 'sayuran')->values(),
        'protein' => $komponen->where('jenis', 'protein')->values(),
    ]);
})->name('customer.kreasikan_salad');

Route::get('/checkout', function () {
    return view('customer.checkout', [
        'tarifOngkir' => Pengaturan::getTarifOngkir(),
        'cabang' => Cabang::all(),
    ]);
})->name('customer.checkout');

Route::post('/checkout', [CheckoutController::class, 'store'])->name('customer.checkout.store');
Route::view('/alamat', 'customer.alamat')->name('customer.alamat');
Route::view('/pembayaran', 'customer.pembayaran')->name('customer.pembayaran');
Route::get('/status-pesanan/{kode?}', [StatusPesananController::class, 'show'])->name('customer.status_pesanan');
Route::post('/status-pesanan/{kode}/upload-bukti', [StatusPesananController::class, 'uploadBukti'])->name('customer.status_pesanan.upload_bukti');
Route::get('/api/pesanan/{kode}/status', [StatusPesananController::class, 'cek'])->name('customer.pesanan.api_status');

Route::post('/status-pesanan/{kode}/rating', [RatingController::class, 'store'])->name('customer.status_pesanan.rating');
Route::post('/pesanan/{kode}/rating', [RatingController::class, 'store']);

Route::post('/midtrans/notification', [MidtransNotificationController::class, 'handle'])->name('midtrans.notification');
Route::view('/cerita-kami', 'customer.cerita')->name('customer.cerita');

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
    Route::patch('/admin/daftar-pesanan/{id}/pembayaran', [PesananController::class, 'updatePembayaran'])
        ->name('admin.daftar_pesanan.pembayaran');
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
    Route::post('/admin/pengaturan/pengiriman', [PengaturanController::class, 'updatePengiriman'])
        ->name('admin.pengaturan.pengiriman');
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

    Route::patch('/admin/kelola-menu/komponen/{id}/status', [MenuController::class, 'toggleKomponenStatus'])
        ->name('admin.kelola_menu.komponen.status');
    Route::patch('/admin/kelola-menu/komponen/{id}', [MenuController::class, 'updateKomponen'])
        ->name('admin.kelola_menu.komponen.update');
    Route::post('/admin/kelola-menu/komponen', [MenuController::class, 'storeKomponen'])
        ->name('admin.kelola_menu.komponen.store');
    Route::delete('/admin/kelola-menu/komponen/{id}', [MenuController::class, 'destroyKomponen'])
        ->name('admin.kelola_menu.komponen.destroy');

    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan');

});
