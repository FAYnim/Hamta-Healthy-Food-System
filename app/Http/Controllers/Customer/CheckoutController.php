<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\DetailPesanan;
use App\Models\KomponenSalad;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\MidtransService;
use App\Services\PromoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Decode cart jika dikirim via FormData multipart sebagai string JSON
        if (is_string($request->input('cart'))) {
            $decoded = json_decode($request->input('cart'), true);
            if (is_array($decoded)) {
                $request->merge(['cart' => $decoded]);
            }
        }

        // Validasi input checkout guest (alamat & bukti bayar QRIS statis wajib diisi)
        $data = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:100'],
            'no_whatsapp' => ['required', 'string', 'max:20'],
            'metode_ambil' => ['required', Rule::in(['pickup', 'delivery'])],
            'alamat_delivery' => ['required_if:metode_ambil,delivery', 'nullable', 'string', 'max:255'],
            'metode_bayar' => ['required', Rule::in(['qris', 'qris_statis', 'tunai', 'midtrans'])],
            'bukti_bayar' => [
                Rule::requiredIf(fn () => $request->input('metode_bayar') === 'qris_statis'),
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120',
            ],
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.tipe' => ['required', Rule::in(['produk', 'custom'])],
            'cart.*.id_produk' => ['required', 'integer'],
            'cart.*.id_komponen' => ['nullable', 'array'],
            'cart.*.id_komponen.*' => ['integer'],
            'cart.*.jumlah' => ['required', 'integer', 'min:1'],
        ], [
            'nama_pelanggan.required' => 'Silakan atur dan pilih alamat pengiriman terlebih dahulu.',
            'no_whatsapp.required' => 'Nomor WhatsApp pemesan wajib terisi dari alamat.',
            'alamat_delivery.required_if' => 'Alamat pengiriman wajib diisi untuk pesanan delivery.',
            'bukti_bayar.required' => 'Bukti pembayaran transfer QRIS wajib diunggah sebelum membuat pesanan.',
            'bukti_bayar.image' => 'File bukti transfer harus berupa gambar.',
            'bukti_bayar.mimes' => 'Format file bukti transfer harus JPG, PNG, atau WEBP.',
            'bukti_bayar.max' => 'Ukuran file bukti transfer maksimal 5MB.',
            'cart.required' => 'Keranjang pesanan masih kosong.',
        ]);

        $buktiBayarPath = null;
        if ($request->hasFile('bukti_bayar')) {
            $buktiBayarPath = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        }

        // ponytail: belum ada pilihan cabang di UI checkout, pakai cabang pertama
        // sebagai fallback sementara; ganti dengan pilihan cabang / cabang terdekat bila sudah ada.
        $cabang = Cabang::orderBy('id_cabang')->first();

        if (! $cabang) {
            throw ValidationException::withMessages([
                'cart' => 'Belum ada cabang yang tersedia, silakan coba lagi nanti.',
            ]);
        }

        // ALUR 1: PEMBAYARAN QRIS (Pickup & Delivery) - Coba Midtrans dulu, jika gagal/down -> fallback_required tanpa insert ke DB
        if (in_array($data['metode_bayar'], ['qris', 'midtrans'])) {
            DB::beginTransaction();

            try {
                // Insert sementara dalam DB transaction untuk hitung promo & ongkir secara akurat
                $pesanan = $this->buatPesananDanItem($data, $cabang, null, 'midtrans', 'pending');
                $pesanan->refresh();
                $grossAmount = (int) round($pesanan->total_bayar);

                // Coba charge Midtrans QRIS
                $midtransOrderId = $pesanan->kode_pesanan.'-'.time();
                $charge = app(MidtransService::class)->chargeQris($midtransOrderId, $grossAmount);

                // Update data Midtrans
                $pesanan->update([
                    'metode_bayar' => 'midtrans',
                    'midtrans_order_id' => $midtransOrderId,
                    'midtrans_response' => $charge,
                    'status_pembayaran' => 'pending',
                ]);

                DB::commit();

                $qrUrl = $charge['qr_url'] ?? null;

                return response()->json([
                    'kode_pesanan' => $pesanan->kode_pesanan,
                    'id_pesanan' => $pesanan->id_pesanan,
                    'total_bayar' => (float) $pesanan->total_bayar,
                    'subtotal' => (float) $pesanan->subtotal,
                    'total_diskon' => (float) $pesanan->total_diskon,
                    'metode_bayar' => $pesanan->metode_bayar,
                    'metode_ambil' => $pesanan->metode_ambil,
                    'nama_pelanggan' => $pesanan->nama_pelanggan,
                    'no_whatsapp' => $pesanan->no_whatsapp,
                    'alamat_delivery' => $pesanan->alamat_delivery,
                    'status_pesanan' => $pesanan->status_pesanan,
                    'status_pembayaran' => $pesanan->status_pembayaran,
                    'qr_url' => $qrUrl,
                    'midtrans_order_id' => $pesanan->midtrans_order_id,
                    'created_at' => $pesanan->created_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i'),
                ], 201);
            } catch (\Throwable $e) {
                DB::rollBack();

                Log::error('Midtrans charge failed: '.$e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'fallback_required' => true,
                    'message' => 'Layanan QRIS otomatis sedang tidak dapat dijangkau. Silakan lakukan pembayaran via QRIS statis di bawah ini dan unggah bukti transfer.',
                ], 200);
            }
        }

        // ALUR 2: QRIS STATIS (SETELAH FALLBACK) / TUNAI
        $metodeBayarFinal = $data['metode_bayar'];
        $statusAwal = 'menunggu_verifikasi';

        $pesanan = DB::transaction(function () use ($data, $cabang, $buktiBayarPath, $metodeBayarFinal, $statusAwal) {
            return $this->buatPesananDanItem($data, $cabang, $buktiBayarPath, $metodeBayarFinal, $statusAwal);
        });

        return response()->json([
            'kode_pesanan' => $pesanan->kode_pesanan,
            'id_pesanan' => $pesanan->id_pesanan,
            'total_bayar' => (float) $pesanan->total_bayar,
            'subtotal' => (float) $pesanan->subtotal,
            'total_diskon' => (float) $pesanan->total_diskon,
            'metode_bayar' => $pesanan->metode_bayar,
            'metode_ambil' => $pesanan->metode_ambil,
            'nama_pelanggan' => $pesanan->nama_pelanggan,
            'no_whatsapp' => $pesanan->no_whatsapp,
            'alamat_delivery' => $pesanan->alamat_delivery,
            'status_pesanan' => $pesanan->status_pesanan,
            'status_pembayaran' => $pesanan->status_pembayaran,
            'qr_url' => null,
            'midtrans_order_id' => $pesanan->midtrans_order_id,
            'created_at' => $pesanan->created_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i'),
        ], 201);
    }

    /**
     * Helper simpan pesanan dan item-item ke database.
     */
    private function buatPesananDanItem(array $data, Cabang $cabang, ?string $buktiBayarPath, string $metodeBayar, string $statusPembayaran): Pesanan
    {
        $produkIds = collect($data['cart'])->pluck('id_produk')->unique()->values();
        $komponenIds = collect($data['cart'])->pluck('id_komponen')->flatten()->unique()->values();

        $produkMap = Produk::whereIn('id_produk', $produkIds)
            ->where('status_aktif', true)
            ->get()
            ->keyBy('id_produk');

        $komponenMap = KomponenSalad::whereIn('id_komponen', $komponenIds)
            ->where('status_aktif', true)
            ->get()
            ->keyBy('id_komponen');

        $subtotal = 0;
        $items = [];

        foreach ($data['cart'] as $item) {
            $produk = $produkMap->get((int) $item['id_produk']);

            if (! $produk) {
                throw ValidationException::withMessages([
                    'cart' => 'Ada produk yang sudah tidak tersedia, silakan perbarui keranjang.',
                ]);
            }

            $komponenTerpilih = collect();

            if ($item['tipe'] === 'custom') {
                $idKomponen = array_values(array_unique($item['id_komponen'] ?? []));

                if (count($idKomponen) === 0) {
                    throw ValidationException::withMessages([
                        'cart' => 'Salad custom wajib memilih minimal satu komponen.',
                    ]);
                }

                $komponenTerpilih = collect($idKomponen)->map(fn ($id) => $komponenMap->get((int) $id));

                if ($komponenTerpilih->contains(null)) {
                    throw ValidationException::withMessages([
                        'cart' => 'Ada komponen salad yang tidak tersedia, silakan perbarui keranjang.',
                    ]);
                }

                $hargaSatuan = (float) $produk->harga_online + (float) $komponenTerpilih->sum('harga_tambahan');
                $namaItem = 'Kreasikan Salad Sendiri';
                $idProdukDetail = null;
            } else {
                $hargaSatuan = (float) $produk->harga_online;
                $namaItem = $produk->nama_produk;
                $idProdukDetail = $produk->id_produk;
            }

            $subtotalItem = round($hargaSatuan * $item['jumlah'], 2);
            $subtotal += $subtotalItem;

            $items[] = [
                'id_produk' => $idProdukDetail,
                'nama_item' => $namaItem,
                'jumlah' => (int) $item['jumlah'],
                'harga_satuan' => $hargaSatuan,
                'subtotal_item' => $subtotalItem,
                'komponen' => $komponenTerpilih,
            ];
        }

        $tarifOngkir = $data['metode_ambil'] === 'delivery' ? Pengaturan::getTarifOngkir() : 0.00;
        $statusOngkir = $data['metode_ambil'] === 'delivery' ? 'terkonfirmasi' : 'tidak_perlu';

        $pesanan = Pesanan::create([
            'kode_pesanan' => $this->generateKodePesanan(),
            'jenis' => 'online',
            'nama_pelanggan' => $data['nama_pelanggan'] ?? null,
            'no_whatsapp' => $data['no_whatsapp'] ?? null,
            'metode_ambil' => $data['metode_ambil'],
            'id_cabang' => $cabang->id_cabang,
            'alamat_delivery' => $data['metode_ambil'] === 'delivery' ? $data['alamat_delivery'] : null,
            'ongkir' => $tarifOngkir,
            'status_ongkir' => $statusOngkir,
            'subtotal' => round($subtotal, 2),
            'total_diskon' => 0,
            'total_bayar' => round($subtotal + $tarifOngkir, 2),
            'metode_bayar' => $metodeBayar,
            'bukti_bayar' => $buktiBayarPath,
            'status_pembayaran' => $statusPembayaran,
            'status_pesanan' => 'diproses',
            'id_admin' => null,
        ]);

        foreach ($items as $item) {
            $detail = DetailPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'id_produk' => $item['id_produk'],
                'nama_item' => $item['nama_item'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga_satuan'],
                'subtotal_item' => $item['subtotal_item'],
                'status_item' => 'aktif',
            ]);

            $pivot = $item['komponen']->mapWithKeys(fn ($komponen) => [
                $komponen->id_komponen => ['harga_tambahan' => $komponen->harga_tambahan],
            ])->toArray();

            if (count($pivot) > 0) {
                $detail->komponenSalad()->attach($pivot);
            }
        }

        $totalPorsi = collect($items)->sum('jumlah');
        app(PromoService::class)->terapkanKePesanan($pesanan, (int) $totalPorsi);

        return $pesanan;
    }

    // Generate kode pesanan unik (format sama dengan KasirController)
    private function generateKodePesanan(): string
    {
        do {
            $kode = 'HMT-'.now()->format('ymd').'-'.strtoupper(Str::random(5));
        } while (Pesanan::where('kode_pesanan', $kode)->exists());

        return $kode;
    }
}
