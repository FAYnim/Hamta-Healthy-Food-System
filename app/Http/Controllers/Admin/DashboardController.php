<?php


namespace App\Http\Controllers\Admin;


use App\Models\Pesanan;
use App\Models\Produk;


class DashboardController extends \App\Http\Controllers\Controller
{
    public function index() {
        // Jumlah pesanan yang masuk hari ini (semua jenis, online & kasir)
        $jumlah_pesanan_hari_ini = Pesanan::whereDate('created_at', today())->count();

        // Jumlah pesanan yang masih diproses, jadi jadi prioritas untuk ditindaklanjuti admin
        $jumlah_pesanan_diproses = Pesanan::where('status_pesanan', 'diproses')->count();

        // Total pendapatan hari ini, hanya dari pesanan yang pembayaran sudah diverifikasi admin
        $pendapatan_hari_ini = Pesanan::where('status_pembayaran', 'terverifikasi')
            ->whereDate('created_at', today())
            ->sum('total_bayar');

        // Jumlah produk yang sedang sold-out (status_aktif = false) untuk alert di dashboard
        $jumlah_produk_sold_out = Produk::where('status_aktif', false)->count();

        // Kirim angka mentah ke view, chart/grafik handled di sisi frontend
        return view('admin.dashboard', [
            'jumlah_pesanan_hari_ini' => $jumlah_pesanan_hari_ini,
            'jumlah_pesanan_diproses' => $jumlah_pesanan_diproses,
            'pendapatan_hari_ini' => $pendapatan_hari_ini,
            'jumlah_produk_sold_out' => $jumlah_produk_sold_out,
        ]);
    }
}
