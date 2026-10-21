<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Pesanan;
use App\Models\DetailPesanan;


class PesananController extends \App\Http\Controllers\Controller
{
    public function index() {
        // Tampilkan semua pesanan (online & kasir) dengan pesanan terbaru di atas
        $pesanan = Pesanan::with(['cabang', 'detailPesanan'])
            ->latest()
            ->get();

        return view('admin.pesanan.index', ['pesanan' => $pesanan]);
    }

    public function konfirmasiOngkir(Request $request, $id) {
        // Validasi tarif ongkir yang dikonfirmasi admin (ongkir sudah dikoreksi manual via WA)
        $data = $request->validate([
            'ongkir' => ['required', 'numeric', 'min:0'],
        ]);

        $pesanan = Pesanan::findOrFail($id);

        // Ongkir hanya relevan untuk pesanan delivery
        if ($pesanan->metode_ambil !== 'delivery') {
            return redirect()->route('admin.pesanan.index')
                ->with('error', 'Ongkir hanya bisa dikonfirmasi untuk pesanan delivery.');
        }

        // Simpan ongkir + tandai sudah terkonfirmasi, lalu hitung ulang total tagihan
        $pesanan->update([
            'ongkir' => $data['ongkir'],
            'status_ongkir' => 'terkonfirmasi',
            // Total bayar = subtotal - diskon + ongkir (dihitung ulang karena sebelumnya ongkir belum ada)
            'total_bayar' => round((float) $pesanan->subtotal - (float) $pesanan->total_diskon + (float) $data['ongkir'], 2),
        ]);

        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Ongkir pesanan ' . $pesanan->kode_pesanan . ' berhasil dikonfirmasi.');
    }

    public function updateStatus(Request $request, $id) {
        // Validasi status baru, hanya boleh salah satu dari status alur pesanan
        $data = $request->validate([
            'status_pesanan' => ['required', Rule::in(['diproses', 'siap', 'selesai', 'batal'])],
        ]);

        $pesanan = Pesanan::findOrFail($id);

        // Status ini berlaku untuk SELURUH pesanan (semua item di dalamnya ikut berstatus sama),
        // beda dengan batalkanItem() yang cuma mengubah satu baris detail saja
        $pesanan->update([
            'status_pesanan' => $data['status_pesanan'],
        ]);

        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Status pesanan ' . $pesanan->kode_pesanan . ' diubah menjadi ' . $data['status_pesanan'] . '.');
    }

    public function batalkanItem(Request $request, $id_detail) {
        // Ambil 1 baris detail item pesanan yang mau dibatalkan
        $detail = DetailPesanan::findOrFail($id_detail);

        // Hanya item ini yang dibatalkan, pesanan utama & item lain TIDAK ikut berubah
        // (dipakai saat satu item kehabisan stok, sisanya tetap diproses)
        $detail->update([
            'status_item' => 'dibatalkan',
        ]);

        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Item "' . $detail->nama_item . '" pada pesanan ' . $detail->pesanan->kode_pesanan . ' dibatalkan.');
    }
}
