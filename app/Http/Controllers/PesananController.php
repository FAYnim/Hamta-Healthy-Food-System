<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Produk;
use App\Models\KomponenSalad;
use App\Services\PromoService;


class PesananController extends Controller
{
    public function store(Request $request) {
        // Validasi input form checkout (guest checkout, customer tidak perlu login)
        $data = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:100'],
            'no_whatsapp' => ['required', 'string', 'max:20'],
            'metode_ambil' => ['required', Rule::in(['pickup', 'delivery'])],
            'id_cabang' => ['required', 'integer', 'exists:cabang,id_cabang'],
            'alamat_delivery' => ['required_if:metode_ambil,delivery', 'nullable', 'string', 'max:255'],
            'metode_bayar' => ['required', Rule::in(['qris', 'tunai'])],
            'bukti_bayar' => ['nullable', 'string', 'max:255'],
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.tipe' => ['required', Rule::in(['produk', 'custom'])],
            'cart.*.id_produk' => ['required', 'integer'],
            'cart.*.id_komponen' => ['nullable', 'array'],
            'cart.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        // Bungkus simpan pesanan + item detail dalam 1 transaksi DB,
        // supaya kalau salah satu bagian gagal semua perubahan dibatalkan (atomic)
        $pesanan = DB::transaction(function () use ($data) {
            // --- HITUNG ULANG HARGA DI BACKEND (bagian paling penting) ---
            // Kumpulkan id produk & id komponen dari keranjang supaya master cukup di-query 1x
            $produkIds = collect($data['cart'])->pluck('id_produk')->unique()->values();
            $komponenIds = collect($data['cart'])->pluck('id_komponen')->flatten()->unique()->values();

            // Ambil master produk (harga_online) dan master komponen (harga_tambahan) dari database
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
                // Harga SELALU diambil dari master produk di DB, bukan dari nilai harga kiriman frontend,
                // jadi customer tidak bisa memanipulasi harga lewat request
                $produk = $produkMap->get((int) $item['id_produk']);

                if (!$produk) {
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

                    // Harga komponen juga diambil dari DB, lalu dijumlahkan dengan harga dasar produk
                    $komponenTerpilih = collect($idKomponen)->map(fn($id) => $komponenMap->get((int) $id));

                    if ($komponenTerpilih->contains(null)) {
                        throw ValidationException::withMessages([
                            'cart' => 'Ada komponen salad yang tidak tersedia, silakan perbarui keranjang.',
                        ]);
                    }

                    $hargaSatuan = (float) $produk->harga_online + (float) $komponenTerpilih->sum('harga_tambahan');
                    $namaItem = 'Kreasikan Salad Sendiri';
                    // id_produk disimpan NULL karena item ini bukan produk reguler (sesuai schema.sql)
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

            // Simpan 1 baris header pesanan (tipe online, menunggu verifikasi pembayaran)
            $pesanan = Pesanan::create([
                'kode_pesanan' => $this->generateKodePesanan(),
                'jenis' => 'online',
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_whatsapp' => $data['no_whatsapp'],
                'metode_ambil' => $data['metode_ambil'],
                'id_cabang' => $data['id_cabang'],
                'alamat_delivery' => $data['alamat_delivery'] ?? null,
                // Ongkir delivery dikonfirmasi admin via WA setelah pesanan dibuat, jadi dikirim NULL
                'ongkir' => $data['metode_ambil'] === 'delivery' ? null : 0,
                'status_ongkir' => $data['metode_ambil'] === 'delivery' ? 'menunggu_konfirmasi' : 'tidak_perlu',
                'subtotal' => round($subtotal, 2),
                // Diskon promo & total tagihan dihitung ulang PromoService setelah detail tersimpan
                'total_diskon' => 0,
                // Total tagihan sementara = subtotal (sebelum promo); ongkir delivery menyusul konfirmasi admin
                'total_bayar' => round($subtotal, 2),
                'metode_bayar' => $data['metode_bayar'],
                // Bukti bayar sudah diupload terpisah, di sini cukup disimpan path-nya
                'bukti_bayar' => $data['bukti_bayar'] ?? null,
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
                $pivot = $item['komponen']->mapWithKeys(fn($komponen) => [
                    $komponen->id_komponen => ['harga_tambahan' => $komponen->harga_tambahan],
                ])->toArray();

                if (count($pivot) > 0) {
                    $detail->komponenSalad()->attach($pivot);
                }
            }

            // Terapkan promo otomatis (boleh stacking) sesuai kondisi pesanan.
            // totalPorsi = jumlah total porsi (quantity) semua item detail.
            // Service ini menghitung ulang total_diskon & total_bayar di dalam transaksi DB yang sama.
            $totalPorsi = collect($items)->sum('jumlah');
            app(PromoService::class)->terapkanKePesanan($pesanan, (int) $totalPorsi);

            return $pesanan;
        });

        // Arahkan customer ke halaman status pesanan lewat kode pesanan
        return redirect()->route('pesanan.show', ['kode_pesanan' => $pesanan->kode_pesanan])
            ->with('success', 'Pesanan berhasil dibuat dengan kode ' . $pesanan->kode_pesanan . '. Silakan selesaikan pembayaran QRIS.');
    }

    public function show($kode_pesanan) {
        // Cari pesanan berdasarkan kode pesanan (bukan id) supaya URL bisa dibagikan ke customer
        $pesanan = Pesanan::with(['cabang', 'detailPesanan.produk', 'detailPesanan.komponenSalad'])
            ->where('kode_pesanan', $kode_pesanan)
            ->firstOrFail();

        return view('status-pesanan', ['pesanan' => $pesanan]);
    }

    // Generate kode pesanan unik untuk dipakai customer saat melacak status pesanan
    private function generateKodePesanan() {
        do {
            $kode = 'HMT-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        } while (Pesanan::where('kode_pesanan', $kode)->exists());

        return $kode;
    }
}
