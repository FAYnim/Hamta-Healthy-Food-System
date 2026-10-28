<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hariIni = today();
        $awalPeriode = $hariIni->copy()->subDays(6)->startOfDay();
        $akhirPeriode = $hariIni->copy()->endOfDay();

        $pesananTerverifikasi = Pesanan::query()
            ->where('status_pembayaran', 'terverifikasi')
            ->where('status_pesanan', '!=', 'batal');

        $pendapatanGrafik = (clone $pesananTerverifikasi)
            ->whereBetween('created_at', [$awalPeriode, $akhirPeriode])
            ->selectRaw('DATE(created_at) as tanggal, SUM(total_bayar) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $grafikPenjualan = collect(range(0, 6))->map(function (int $offset) use ($hariIni, $pendapatanGrafik): array {
            $tanggal = $hariIni->copy()->subDays(6 - $offset);
            $tanggalKey = $tanggal->toDateString();

            return [
                'tanggal' => $tanggalKey,
                'label' => $tanggal->format('d/m'),
                'total' => (float) ($pendapatanGrafik[$tanggalKey] ?? 0),
            ];
        });

        $notifikasiTerbaru = Pesanan::query()
            ->with(['detailPesanan' => fn ($query) => $query->where('status_item', 'aktif')])
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'jumlah_pesanan_hari_ini' => Pesanan::whereDate('created_at', $hariIni)->count(),
            'pendapatan_hari_ini' => (clone $pesananTerverifikasi)
                ->whereDate('created_at', $hariIni)
                ->sum('total_bayar'),
            'jumlah_menunggu_deposit' => Pesanan::where('status_pembayaran', 'menunggu_verifikasi')->count(),
            'jumlah_pesanan_selesai' => Pesanan::where('status_pesanan', 'selesai')->count(),
            'grafik_penjualan' => $grafikPenjualan,
            'notifikasi_terbaru' => $notifikasiTerbaru,
            'jumlah_produk_sold_out' => Produk::where('status_aktif', false)->count(),
        ]);
    }
}
