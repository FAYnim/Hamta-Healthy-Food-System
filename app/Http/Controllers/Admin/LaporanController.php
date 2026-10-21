<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Models\DetailPesanan;


class LaporanController extends \App\Http\Controllers\Controller
{
    public function index(Request $request) {
        // Validasi filter periode dari form laporan (boleh kosong, nanti default bulan berjalan)
        $data = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        // Default filter = bulan berjalan (tanggal 1 sampai hari terakhir bulan ini)
        $tanggal_mulai = $data['tanggal_mulai'] ?? now()->startOfMonth()->toDateString();
        $tanggal_selesai = $data['tanggal_selesai'] ?? now()->endOfMonth()->toDateString();

        // Laporan diambil dari tabel detail_pesanan (satu baris per item),
        // lalu dikelompokkan per nama_item: total porsi terjual & total pendapatan per item.
        // SUM(...) di selectRaw dipakai hanya untuk agregasi, datanya tetap lewat Eloquent.
        $laporan = DetailPesanan::query()
            ->selectRaw('nama_item, SUM(jumlah) as total_terjual, SUM(subtotal_item) as total_pendapatan')
            // Filter pesanan induknya lewat relasi: hanya pesanan yang pembayarannya sudah diverifikasi
            // dan tanggal pesanannya berada di dalam rentang filter
            ->whereHas('pesanan', function ($query) use ($tanggal_mulai, $tanggal_selesai) {
                $query->whereDate('created_at', '>=', $tanggal_mulai)
                    ->whereDate('created_at', '<=', $tanggal_selesai)
                    ->where('status_pembayaran', 'terverifikasi');
            })
            // Item yang dibatalkan admin (kehabisan stok) tidak ikut dihitung
            ->where('status_item', 'aktif')
            // Kelompokkan per nama item, urutkan dari yang paling banyak terjual
            ->groupBy('nama_item')
            ->orderByDesc('total_terjual')
            ->get();

        // Total pendapatan seluruh item (dihitung dari hasil di atas, tanpa query tambahan)
        $total_pendapatan = $laporan->sum('total_pendapatan');

        return view('admin.laporan', [
            'laporan' => $laporan,
            'total_pendapatan' => $total_pendapatan,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
        ]);
    }
}
