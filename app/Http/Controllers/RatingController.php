<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Pesanan;


class RatingController extends Controller
{
    public function store(Request $request, $kode_pesanan) {
        // Validasi input ulasan: bintang wajib 1-5, ulasan teks opsional
        $data = $request->validate([
            'bintang' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string', 'max:500'],
        ]);

        // Cari pesanan berdasarkan kode pesanan untuk mendapat id_pesanan-nya
        $pesanan = Pesanan::where('kode_pesanan', $kode_pesanan)
            ->firstOrFail();

        // Satu pesanan hanya boleh dirating sekali (kolom id_pesanan di tabel rating itu UNIQUE),
        // jadi dicek dulu supaya user tidak melihat error database mentah
        if (Rating::where('id_pesanan', $pesanan->id_pesanan)->exists()) {
            return redirect()->route('pesanan.show', ['kode_pesanan' => $kode_pesanan])
                ->with('error', 'Pesanan ini sudah pernah dirating, jadi ulasan tidak bisa dikirim ulang.');
        }

        // Simpan rating baru untuk pesanan tersebut
        Rating::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'bintang' => $data['bintang'],
            'ulasan' => $data['ulasan'] ?? null,
        ]);

        // Kembalikan customer ke halaman status pesanan
        return redirect()->route('pesanan.show', ['kode_pesanan' => $kode_pesanan])
            ->with('success', 'Terima kasih! Ulasan Anda sudah tersimpan.');
    }
}
