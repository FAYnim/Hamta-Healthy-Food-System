<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Produk;
use App\Models\KomponenSalad;
use App\Models\Cabang;
use App\Models\Promo;
use App\Services\PromoService;


class KasirController extends \App\Http\Controllers\Controller
{
    public function index() {
        // Produk aktif saja yang bisa dipilih kasir (produk sold-out disembunyikan)
        $produk = Produk::where('status_aktif', true)
            ->orderBy('nama_produk')
            ->get();

        // Komponen salad aktif, dipisah per jenis (sayuran & protein) buat section di form kasir
        $komponen = KomponenSalad::where('status_aktif', true)->get();
        $sayuran = $komponen->where('jenis', 'sayuran')->values();
        $protein = $komponen->where('jenis', 'protein')->values();

        // Daftar cabang untuk dipilih kasir saat input transaksi
        $cabang = Cabang::orderBy('nama_cabang')->get();

        // Promo yang sedang berlaku, biar kasir tahu promo mana yang bisa dipakai customer.
        // Promo tetap diterapkan otomatis oleh PromoService, ini cuma informasi.
        $promoAktif = Promo::where('status_aktif', true)
            ->whereColumn('jumlah_terpakai', '<', 'batas_penggunaan')
            ->orderBy('nama_promo')
            ->get();

        return view('admin.kasir', [
            'produk' => $produk,
            'sayuran' => $sayuran,
            'protein' => $protein,
            'cabang' => $cabang,
            'promoAktif' => $promoAktif,
        ]);
    }

    public function store(Request $request) {
        // Validasi input transaksi kasir (nama pelanggan boleh kosong untuk walk-in)
        $data = $request->validate([
            'nama_pelanggan' => ['nullable', 'string', 'max:100'],
            'no_whatsapp' => ['nullable', 'string', 'max:20'],
            'id_cabang' => ['required', 'integer', 'exists:cabang,id_cabang'],
            'metode_bayar' => ['required', Rule::in(['tunai', 'qris'])],
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.tipe' => ['required', Rule::in(['produk', 'custom'])],
            'cart.*.id_produk' => ['required', 'integer'],
            'cart.*.id_komponen' => ['nullable', 'array'],
            'cart.*.jumlah' => ['required', 'integer', 'min:1'],
        ]);

        // Id admin diambil dari session login, tanpa itu transaksi tidak bisa disimpan
        $id_admin = session('admin_id');

        if (!$id_admin) {
            throw ValidationException::withMessages([
                'cart' => 'Sesi admin habis, silakan login ulang.',
            ]);
        }

        // Bungkus proses simpan pesanan + detail dalam 1 transaksi DB (sama seperti checkout customer)
        $pesanan = DB::transaction(function () use ($data, $id_admin) {
            // --- HITUNG ULANG HARGA DI BACKEND (sama seperti PesananController customer) ---
            // Kumpulkan id produk & id komponen dari keranjang supaya master cukup di-query 1x
            $produkIds = collect($data['cart'])->pluck('id_produk')->unique()->values();
            $komponenIds = collect($data['cart'])->pluck('id_komponen')->flatten()->unique()->values();

            // Ambil master produk & master komponen dari database
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
                // Harga SELALU diambil dari master produk di DB, bukan dari nilai harga kiriman frontend
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

                    // BEDA dengan kasir: pakai harga_offline (bukan harga_online seperti checkout customer),
                    // karena transaksi di kasir tidak pakai harga channel online
                    $hargaSatuan = (float) $produk->harga_offline + (float) $komponenTerpilih->sum('harga_tambahan');
                    $namaItem = 'Kreasikan Salad Sendiri';
                    // id_produk disimpan NULL karena item ini bukan produk reguler (sesuai schema.sql)
                    $idProdukDetail = null;
                } else {
                    // Produk reguler: harga satuan memakai harga_offline dari master produk
                    $hargaSatuan = (float) $produk->harga_offline;
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

            // Simpan 1 baris header pesanan Offline (jenis = kasir)
            $pesanan = Pesanan::create([
                'kode_pesanan' => $this->generateKodePesanan(),
                'jenis' => 'kasir',
                // BEDA dengan customer: nama pelanggan boleh NULL untuk transaksi walk-in
                'nama_pelanggan' => $data['nama_pelanggan'] ?? null,
                'no_whatsapp' => $data['no_whatsapp'] ?? null,
                // Transaksi kasir diasumsikan customer ambil di tempat, jadi tidak ada ongkir
                'metode_ambil' => 'pickup',
                'id_cabang' => $data['id_cabang'],
                'alamat_delivery' => null,
                'ongkir' => 0,
                'status_ongkir' => 'tidak_perlu',
                'subtotal' => round($subtotal, 2),
                'total_diskon' => 0,
                'total_bayar' => round($subtotal, 2),
                'metode_bayar' => $data['metode_bayar'],
                'bukti_bayar' => null,
                // BEDA dengan customer: pembayaran di kasir langsung diterima/diterima kasir,
                // jadi langsung terverifikasi tanpa menunggu upload bukti bayar
                'status_pembayaran' => 'terverifikasi',
                'status_pesanan' => 'diproses',
                'id_admin' => $id_admin,
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

            // Terapkan promo otomatis (boleh stacking) sesuai kondisi pesanan kasir.
            // totalPorsi = jumlah total porsi (quantity) semua item detail.
            // Service ini menghitung ulang total_diskon & total_bayar di dalam transaksi DB yang sama.
            $totalPorsi = collect($items)->sum('jumlah');
            app(PromoService::class)->terapkanKePesanan($pesanan, (int) $totalPorsi);

            return $pesanan;
        });

        // Nama promo yang terpakai, ditampilkan di pesan sukses biar kasir tahu diskonnya
        $namaPromo = $pesanan->promo->pluck('nama_promo')->join(', ');

        // Kembali ke halaman kasir dengan pesan sukses
        return redirect()->route('admin.kasir')
            ->with('success', 'Transaksi kasir tersimpan dengan kode pesanan ' . $pesanan->kode_pesanan . '.'
                . ($namaPromo !== '' ? ' Promo terpakai: ' . $namaPromo . '.' : ''));
    }

    // Generate kode pesanan unik untuk transaksi kasir (format sama dengan pesanan online)
    private function generateKodePesanan() {
        do {
            $kode = 'HMT-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        } while (Pesanan::where('kode_pesanan', $kode)->exists());

        return $kode;
    }
}
