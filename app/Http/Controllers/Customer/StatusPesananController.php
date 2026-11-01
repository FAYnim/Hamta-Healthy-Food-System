<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusPesananController extends Controller
{
    /**
     * Tampilkan halaman pelacakan status pesanan pelanggan.
     */
    public function show(Request $request, ?string $kode = null): View
    {
        $kodePesanan = trim((string) ($kode ?: $request->query('kode', '')));
        $pesanan = null;

        if ($kodePesanan !== '') {
            $pesanan = Pesanan::with(['cabang', 'detailPesanan.komponenSalad'])
                ->where('kode_pesanan', $kodePesanan)
                ->first();
        }

        return view('customer.status-pesanan', [
            'pesanan' => $pesanan,
            'kode' => $kodePesanan,
        ]);
    }

    /**
     * Endpoint API JSON untuk pengecekan status pesanan secara live (realtime polling).
     */
    public function cek(string $kode): JsonResponse
    {
        $pesanan = Pesanan::with(['cabang', 'detailPesanan.komponenSalad'])
            ->where('kode_pesanan', trim($kode))
            ->first();

        if (! $pesanan) {
            return response()->json([
                'ok' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $items = $pesanan->detailPesanan->map(function ($item) {
            return [
                'id_detail' => $item->id_detail,
                'nama_item' => $item->nama_item,
                'jumlah' => (int) $item->jumlah,
                'harga_satuan' => (float) $item->harga_satuan,
                'subtotal_item' => (float) $item->subtotal_item,
                'status_item' => $item->status_item,
                'komponen' => $item->komponenSalad->pluck('nama_komponen')->all(),
            ];
        });

        return response()->json([
            'ok' => true,
            'kode_pesanan' => $pesanan->kode_pesanan,
            'status_pesanan' => $pesanan->status_pesanan,
            'status_pembayaran' => $pesanan->status_pembayaran,
            'status_ongkir' => $pesanan->status_ongkir,
            'ongkir' => (float) $pesanan->ongkir,
            'subtotal' => (float) $pesanan->subtotal,
            'total_diskon' => (float) $pesanan->total_diskon,
            'total_bayar' => (float) $pesanan->total_bayar,
            'metode_ambil' => $pesanan->metode_ambil,
            'alamat_delivery' => $pesanan->alamat_delivery,
            'nama_cabang' => $pesanan->cabang?->nama_cabang ?? 'Cabang Hamta',
            'metode_bayar' => $pesanan->metode_bayar,
            'nama_pelanggan' => $pesanan->nama_pelanggan,
            'items' => $items,
            'updated_at' => $pesanan->updated_at ? $pesanan->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : null,
        ]);
    }
}
