<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\DetailPesanan;
use App\Models\KomponenSalad;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Services\PromoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Validasi input checkout guest (tanpa auth, alamat hanya snapshot string).
        // Nama & WA nullable mengikuti kolom DB (pickup tanpa alamat tersimpan tetap bisa order).
        $data = $request->validate([
            'nama_pelanggan' => ['nullable', 'string', 'max:100'],
            'no_whatsapp' => ['nullable', 'string', 'max:20'],
            'metode_ambil' => ['required', Rule::in(['pickup', 'delivery'])],
            'alamat_delivery' => ['required_if:metode_ambil,delivery', 'nullable', 'string', 'max:255'],
            'metode_bayar' => ['required', Rule::in(['qris', 'tunai'])],
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.tipe' => ['required', Rule::in(['produk', 'custom'])],
            'cart.*.id_produk' => ['required', 'integer'],
            'cart.*.id_komponen' => ['nullable', 'array'],
            'cart.*.id_komponen.*' => ['integer'],
            'cart.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        // ponytail: belum ada pilihan cabang di UI checkout, pakai cabang pertama
        // sebagai fallback sementara; ganti dengan pilihan cabang / cabang terdekat bila sudah ada.
        $cabang = Cabang::orderBy('id_cabang')->first();

        if (! $cabang) {
            throw ValidationException::withMessages([
                'cart' => 'Belum ada cabang yang tersedia, silakan coba lagi nanti.',
            ]);
        }

        // Bungkus simpan pesanan + detail dalam 1 transaksi DB (pola sama seperti KasirController@store)
        $pesanan = DB::transaction(function () use ($data, $cabang) {
            // Kumpulkan id produk & id komponen dari keranjang supaya master cukup di-query 1x
            $produkIds = collect($data['cart'])->pluck('id_produk')->unique()->values();
            $komponenIds = collect($data['cart'])->pluck('id_komponen')->flatten()->unique()->values();

            // Harga SELALU dari master DB (harga_online), bukan dari kiriman frontend
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
                    // Salad custom wajib memilih minimal satu komponen (sayuran/protein)
                    $idKomponen = array_values(array_unique($item['id_komponen'] ?? []));

                    if (count($idKomponen) === 0) {
                        throw ValidationException::withMessages([
                            'cart' => 'Salad custom wajib memilih minimal satu komponen.',
                        ]);
                    }

                    // Harga komponen juga dari DB, dijumlahkan dengan harga dasar produk
                    $komponenTerpilih = collect($idKomponen)->map(fn ($id) => $komponenMap->get((int) $id));

                    if ($komponenTerpilih->contains(null)) {
                        throw ValidationException::withMessages([
                            'cart' => 'Ada komponen salad yang tidak tersedia, silakan perbarui keranjang.',
                        ]);
                    }

                    $hargaSatuan = (float) $produk->harga_online + (float) $komponenTerpilih->sum('harga_tambahan');
                    $namaItem = 'Kreasikan Salad Sendiri';
                    // id_produk NULL karena item ini bukan produk reguler (sesuai skema detail_pesanan)
                    $idProdukDetail = null;
                } else {
                    // Produk reguler: harga satuan memakai harga_online dari master produk
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

            // Simpan 1 baris header pesanan online (menunggu verifikasi pembayaran)
            $pesanan = Pesanan::create([
                'kode_pesanan' => $this->generateKodePesanan(),
                'jenis' => 'online',
                'nama_pelanggan' => $data['nama_pelanggan'] ?? null,
                'no_whatsapp' => $data['no_whatsapp'] ?? null,
                'metode_ambil' => $data['metode_ambil'],
                'id_cabang' => $cabang->id_cabang,
                'alamat_delivery' => $data['metode_ambil'] === 'delivery' ? $data['alamat_delivery'] : null,
                // Ongkir delivery NULL = menunggu konfirmasi admin via WA; pickup tidak perlu ongkir
                'ongkir' => $data['metode_ambil'] === 'delivery' ? null : 0,
                'status_ongkir' => $data['metode_ambil'] === 'delivery' ? 'menunggu_konfirmasi' : 'tidak_perlu',
                'subtotal' => round($subtotal, 2),
                'total_diskon' => 0,
                // Total sementara = subtotal (sebelum promo); ongkir delivery menyusul konfirmasi admin
                'total_bayar' => round($subtotal, 2),
                'metode_bayar' => $data['metode_bayar'],
                'bukti_bayar' => null,
                'status_pembayaran' => 'menunggu_verifikasi',
                'status_pesanan' => 'diproses',
                'id_admin' => null,
            ]);

            // Simpan item-item pesanan ke tabel detail_pesanan
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

                // Simpan pilihan komponen salad custom + snapshot harga_tambahan saat transaksi
                $pivot = $item['komponen']->mapWithKeys(fn ($komponen) => [
                    $komponen->id_komponen => ['harga_tambahan' => $komponen->harga_tambahan],
                ])->toArray();

                if (count($pivot) > 0) {
                    $detail->komponenSalad()->attach($pivot);
                }
            }

            // Terapkan promo otomatis (boleh stacking); hitung ulang total_diskon & total_bayar
            $totalPorsi = collect($items)->sum('jumlah');
            app(PromoService::class)->terapkanKePesanan($pesanan, (int) $totalPorsi);

            return $pesanan;
        });

        return response()->json([
            'kode_pesanan' => $pesanan->kode_pesanan,
            'id_pesanan' => $pesanan->id_pesanan,
        ], 201);
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
