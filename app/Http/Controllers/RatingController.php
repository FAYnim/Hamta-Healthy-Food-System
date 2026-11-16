<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, string $kode_pesanan)
    {
        // Validasi input ulasan: bintang wajib 1-5, ulasan teks opsional
        $data = $request->validate([
            'bintang' => ['required', 'integer', 'min:1', 'max:5'],
            'ulasan' => ['nullable', 'string', 'max:500'],
        ]);

        // Cari pesanan berdasarkan kode pesanan untuk mendapat id_pesanan-nya
        $pesanan = Pesanan::where('kode_pesanan', trim($kode_pesanan))
            ->firstOrFail();

        // Satu pesanan hanya boleh dirating sekali (kolom id_pesanan di tabel rating itu UNIQUE)
        if (Rating::where('id_pesanan', $pesanan->id_pesanan)->exists()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Pesanan ini sudah pernah dirating, jadi ulasan tidak bisa dikirim ulang.',
                ], 422);
            }

            return redirect()->route('customer.status_pesanan', ['kode' => $pesanan->kode_pesanan])
                ->with('error', 'Pesanan ini sudah pernah dirating, jadi ulasan tidak bisa dikirim ulang.');
        }

        // Simpan rating baru untuk pesanan tersebut
        $rating = Rating::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'bintang' => $data['bintang'],
            'ulasan' => $data['ulasan'] ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'message' => 'Terima kasih! Ulasan Anda sudah tersimpan.',
                'rating' => $rating,
            ]);
        }

        // Kembalikan customer ke halaman status pesanan
        return redirect()->route('customer.status_pesanan', ['kode' => $pesanan->kode_pesanan])
            ->with('success', 'Terima kasih! Ulasan Anda sudah tersimpan.');
    }
}
