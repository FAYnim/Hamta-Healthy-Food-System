<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Carbon\Carbon;
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

        $qrUrl = null;
        if (! empty($pesanan->midtrans_response['qr_url'])) {
            $qrUrl = $pesanan->midtrans_response['qr_url'];
        } elseif (! empty($pesanan->midtrans_response['actions'])) {
            foreach ($pesanan->midtrans_response['actions'] as $act) {
                if (($act['name'] ?? '') === 'generate-qr-code') {
                    $qrUrl = $act['url'] ?? null;
                    break;
                }
            }
        }

        $expiryTime = $pesanan->midtrans_response['expiry_time'] ?? null;
        $isExpired = false;
        if ($expiryTime && in_array($pesanan->status_pembayaran, ['menunggu_verifikasi', 'pending'])) {
            try {
                if (now()->greaterThanOrEqualTo(Carbon::parse($expiryTime))) {
                    $isExpired = true;
                }
            } catch (\Throwable) {
                $isExpired = false;
            }
        }

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
            'qr_url' => $qrUrl,
            'expiry_time' => $expiryTime,
            'is_expired' => $isExpired,
            'bukti_bayar' => $pesanan->bukti_bayar ? asset('storage/'.$pesanan->bukti_bayar) : null,
            'nama_pelanggan' => $pesanan->nama_pelanggan,
            'items' => $items,
            'updated_at' => $pesanan->updated_at ? $pesanan->updated_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : null,
        ]);
    }

    /**
     * Upload bukti transfer pembayaran manual (QRIS Statis).
     */
    public function uploadBukti(Request $request, string $kode)
    {
        $pesanan = Pesanan::where('kode_pesanan', trim($kode))->firstOrFail();

        $request->validate([
            'bukti_bayar' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'bukti_bayar.required' => 'Silakan pilih file bukti transfer.',
            'bukti_bayar.image' => 'File bukti transfer harus berupa gambar.',
            'bukti_bayar.mimes' => 'Format file harus JPG, PNG, atau WEBP.',
            'bukti_bayar.max' => 'Ukuran file bukti transfer maksimal 5MB.',
        ]);

        $path = $request->file('bukti_bayar')->store('bukti_bayar', 'public');

        $pesanan->update([
            'bukti_bayar' => $path,
            'metode_bayar' => 'qris_statis',
            'status_pembayaran' => 'menunggu_verifikasi',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi kasir.',
                'bukti_bayar' => asset('storage/'.$path),
            ]);
        }

        return redirect()->route('customer.status_pesanan', ['kode' => $pesanan->kode_pesanan])
            ->with('success', 'Bukti transfer berhasil diunggah. Menunggu verifikasi tim kasir.');
    }
}
