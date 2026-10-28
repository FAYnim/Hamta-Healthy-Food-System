<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Services\PromoService;


class PesananController extends \App\Http\Controllers\Controller
{
    public function index(Request $request) {
        // Tampilkan semua pesanan (online & kasir), filter status + pencarian opsional
        $status = $request->query('status');
        $q = trim((string) $request->query('q'));

        $pesanan = Pesanan::with(['cabang'])
            ->when(in_array($status, ['diproses', 'siap', 'selesai', 'batal'], true),
                fn($query) => $query->where('status_pesanan', $status))
            ->when($q !== '', fn($query) => $query->where(fn($sub) => $sub
                ->where('kode_pesanan', 'like', "%{$q}%")
                ->orWhere('nama_pelanggan', 'like', "%{$q}%")
                ->orWhere('no_whatsapp', 'like', "%{$q}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.daftar_pesanan', [
            'pesanan' => $pesanan,
            'status' => $status,
            'q' => $q,
        ]);
    }

    public function show($id) {
        // Detail 1 pesanan untuk panel drawer (relasi lengkap utk tampilan)
        $pesanan = Pesanan::with(['cabang', 'admin', 'detailPesanan.komponenSalad', 'promo', 'rating'])
            ->findOrFail($id);

        return view('admin.partials.pesanan_detail', ['pesanan' => $pesanan]);
    }

    public function konfirmasiOngkir(Request $request, $id) {
        // Validasi tarif ongkir yang dikonfirmasi admin (ongkir sudah dikoreksi manual via WA)
        $data = $request->validate([
            'ongkir' => ['required', 'numeric', 'min:0'],
        ]);

        $pesanan = Pesanan::findOrFail($id);

        // Ongkir hanya relevan untuk pesanan delivery
        if ($pesanan->metode_ambil !== 'delivery') {
            return redirect()->route('admin.daftar_pesanan')
                ->with('error', 'Ongkir hanya bisa dikonfirmasi untuk pesanan delivery.');
        }

        // Simpan ongkir + tandai sudah terkonfirmasi, lalu hitung ulang total tagihan
        $pesanan->update([
            'ongkir' => $data['ongkir'],
            'status_ongkir' => 'terkonfirmasi',
        ]);

        // Hitung ulang diskon & total bayar (subtotal - diskon + ongkir).
        // Promo gratis ongkir baru ada nilainya di sini, karena tarif ongkir
        // baru diketahui setelah admin mengonfirmasi.
        app(PromoService::class)->terapkanUlang($pesanan);

        // Dari loker (ajax) → balik partial segar supaya panel tetap terbuka
        if ($request->ajax()) {
            return $this->show($pesanan->id_pesanan);
        }

        return redirect()->route('admin.daftar_pesanan')
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

        // Dari loker (ajax) → balik partial segar supaya panel tetap terbuka
        if ($request->ajax()) {
            return $this->show($pesanan->id_pesanan);
        }

        return redirect()->route('admin.daftar_pesanan')
            ->with('success', 'Status pesanan ' . $pesanan->kode_pesanan . ' diubah menjadi ' . $data['status_pesanan'] . '.');
    }

    public function batalkanItem(Request $request, $id_detail) {
        // Ambil 1 baris detail item pesanan yang mau dibatalkan
        $detail = DetailPesanan::findOrFail($id_detail);
        $pesanan = $detail->pesanan;

        if ($detail->status_item === 'dibatalkan') {
            // Sudah dibatalkan sebelumnya — tidak ada perubahan, hanya beri pesan error
            $flash = 'error';
            $pesan = 'Item "' . $detail->nama_item . '" pada pesanan ' . $pesanan->kode_pesanan . ' sudah dibatalkan sebelumnya.';
        } else {
            // Hanya item ini yang dibatalkan, pesanan utama & item lain TIDAK ikut berubah
            // (dipakai saat satu item kehabisan stok, sisanya tetap diproses)
            $detail->update([
                'status_item' => 'dibatalkan',
            ]);

            // Hitung ulang subtotal & diskon pesanan induk (item batal tidak dihitung).
            // Promo ikut dihitung ulang karena diskon persen bergantung pada subtotal,
            // dan total bayar tidak boleh jadi negatif.
            $subtotalBaru = $pesanan->detailPesanan()->where('status_item', '!=', 'dibatalkan')->sum('subtotal_item');

            app(PromoService::class)->terapkanUlang($pesanan, (float) $subtotalBaru);

            $flash = 'success';
            $pesan = 'Item "' . $detail->nama_item . '" pada pesanan ' . $pesanan->kode_pesanan . ' dibatalkan.';
        }

        // Dari loker (ajax) → balik partial segar supaya panel tetap terbuka
        if ($request->ajax()) {
            return $this->show($pesanan->id_pesanan);
        }

        return redirect()->route('admin.daftar_pesanan')->with($flash, $pesan);
    }
}
