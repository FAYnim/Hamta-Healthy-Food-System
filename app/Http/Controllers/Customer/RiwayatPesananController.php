<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatPesananController extends Controller
{
    /**
     * Tampilkan halaman riwayat pemesanan customer.
     */
    public function index(): View
    {
        return view('customer.riwayat-pesanan');
    }

    /**
     * Endpoint API untuk memuat riwayat pesanan berdasarkan kode-kode di localStorage
     * atau pencarian nomor WhatsApp / kode pesanan.
     */
    public function fetchHistory(Request $request): JsonResponse
    {
        $codes = $request->input('codes', []);
        $phone = trim((string) $request->input('phone', ''));
        $search = trim((string) $request->input('search', ''));

        $query = Pesanan::with(['cabang', 'detailPesanan.komponenSalad', 'detailPesanan.produk']);

        if (! empty($codes) && is_array($codes)) {
            // Ambil berdasarkan kumpulan kode pesanan di browser
            $filteredCodes = array_slice(array_filter($codes, 'is_string'), 0, 50);
            $query->whereIn('kode_pesanan', $filteredCodes);
        } elseif ($phone !== '') {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            // Tangani pencarian nomor telepon lokal/internasional (08xxx vs 628xxx)
            $phoneVariants = [$phone, $cleanPhone];
            if (str_starts_with($cleanPhone, '0')) {
                $phoneVariants[] = '62'.substr($cleanPhone, 1);
            } elseif (str_starts_with($cleanPhone, '62')) {
                $phoneVariants[] = '0'.substr($cleanPhone, 2);
            }

            $query->where(function ($sub) use ($phoneVariants) {
                foreach ($phoneVariants as $var) {
                    if ($var !== '') {
                        $sub->orWhere('no_whatsapp', 'like', "%{$var}%");
                    }
                }
            });
        } elseif ($search !== '') {
            $query->where('kode_pesanan', 'like', "%{$search}%");
        } else {
            return response()->json([
                'ok' => true,
                'pesanan' => [],
            ]);
        }

        $pesananList = $query->latest('id_pesanan')->limit(30)->get();

        $data = $pesananList->map(function ($p) {
            $items = $p->detailPesanan->map(function ($item) {
                return [
                    'id_detail' => $item->id_detail,
                    'id_produk' => $item->id_produk,
                    'nama_item' => $item->nama_item,
                    'jumlah' => (int) $item->jumlah,
                    'harga_satuan' => (float) $item->harga_satuan,
                    'subtotal_item' => (float) $item->subtotal_item,
                    'status_item' => $item->status_item,
                    'foto' => $item->produk?->foto ? asset('storage/'.$item->produk->foto) : ($item->id_produk ? null : asset('images/salad-1-clean.webp')),
                    'komponen' => $item->komponenSalad->pluck('nama_komponen')->all(),
                    'id_komponen' => $item->komponenSalad->pluck('id_komponen')->all(),
                ];
            });

            return [
                'id_pesanan' => $p->id_pesanan,
                'kode_pesanan' => $p->kode_pesanan,
                'nama_pelanggan' => $p->nama_pelanggan ?: 'Pelanggan',
                'no_whatsapp' => $p->no_whatsapp,
                'metode_ambil' => $p->metode_ambil,
                'alamat_delivery' => $p->alamat_delivery,
                'nama_cabang' => $p->cabang?->nama_cabang ?? 'Cabang Hamta',
                'subtotal' => (float) $p->subtotal,
                'ongkir' => (float) $p->ongkir,
                'total_diskon' => (float) $p->total_diskon,
                'total_bayar' => (float) $p->total_bayar,
                'metode_bayar' => $p->metode_bayar,
                'status_pembayaran' => $p->status_pembayaran,
                'status_pesanan' => $p->status_pesanan,
                'total_item' => (int) $p->detailPesanan->sum('jumlah'),
                'items' => $items,
                'created_at_formatted' => $p->created_at ? $p->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : '-',
                'created_at_human' => $p->created_at ? $p->created_at->diffForHumans() : '-',
            ];
        });

        return response()->json([
            'ok' => true,
            'pesanan' => $data,
        ]);
    }
}
