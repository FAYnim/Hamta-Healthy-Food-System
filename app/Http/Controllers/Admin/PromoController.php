<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Promo;
use App\Services\PromoService;


class PromoController extends \App\Http\Controllers\Controller
{
    public function index(Request $request) {
        // Tampilkan semua promo (aktif & nonaktif) supaya admin bisa monitored & diaktifkan kembali
        $q = trim((string) $request->query('q'));

        $promo = Promo::query()
            // Filter cari by nama promo (opsional, bila kolom q diisi)
            ->when($q !== '', fn ($query) => $query->where('nama_promo', 'like', "%{$q}%"))
            ->orderBy('nama_promo')
            ->get();

        return view('admin.promo', ['promo' => $promo, 'q' => $q]);
    }

    public function toggleStatus(Request $request, $id) {
        // Validasi input: status_aktif harus boolean (toggle kirim 1/0)
        $data = $request->validate([
            'status_aktif' => ['required', 'boolean'],
        ]);

        $promo = Promo::findOrFail($id);

        // Mengaktifkan promo yang kuotanya sudah habis tidak diizinkan
        if ($data['status_aktif'] && $promo->jumlah_terpakai >= $promo->batas_penggunaan) {
            return redirect()->route('admin.promo.index')
                ->with('error', 'Promo "' . $promo->nama_promo . '" tidak bisa diaktifkan karena kuota sudah habis (' . $promo->jumlah_terpakai . '/' . $promo->batas_penggunaan . ').');
        }

        $promo->update(['status_aktif' => (bool) $data['status_aktif']]);

        // Kalau toggle menyisakan kuota habis, nonaktifkan lagi (konsistensi)
        $this->promoService()->nonaktifkanJikaHabis($promo);

        return redirect()->route('admin.promo.index')
            ->with('success', 'Status promo "' . $promo->nama_promo . '" berhasil diperbarui.');
    }

    public function store(Request $request) {
        // Validasi input form tambah promo
        $data = $request->validate($this->rulesValidasi($request));

        // Simpan promo baru ke database (jumlah_terpakai mulai dari 0, diisi sistem saat promo dipakai)
        $promo = Promo::create([
            'nama_promo' => $data['nama_promo'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tipe_potongan' => $data['tipe_potongan'],
            // Gratis ongkir tidak punya nilai potongan nominal
            'nilai_potongan' => $data['tipe_potongan'] === 'gratis_ongkir' ? null : $data['nilai_potongan'],
            'syarat_min_porsi' => $data['syarat_min_porsi'] ?? null,
            'batas_penggunaan' => $data['batas_penggunaan'],
            'jumlah_terpakai' => 0,
            'status_aktif' => $data['status_aktif'] ?? true,
            'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
        ]);

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo "' . $promo->nama_promo . '" berhasil ditambahkan.');
    }

    public function update(Request $request, $id) {
        // Validasi input form edit promo (sama persis dengan form tambah)
        $data = $request->validate($this->rulesValidasi($request));

        // Update data promo yang dipilih (jumlah_terpakai tidak diedit, itu diurus sistem)
        $promo = Promo::findOrFail($id);

        $promo->update([
            'nama_promo' => $data['nama_promo'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tipe_potongan' => $data['tipe_potongan'],
            'nilai_potongan' => $data['tipe_potongan'] === 'gratis_ongkir' ? null : $data['nilai_potongan'],
            'syarat_min_porsi' => $data['syarat_min_porsi'] ?? null,
            'batas_penggunaan' => $data['batas_penggunaan'],
            // Status tidak dikirim form (mis. form edit lama) → pertahankan status promo yang sekarang
            'status_aktif' => $data['status_aktif'] ?? $promo->status_aktif,
            'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
        ]);

        // Cek lagi kuota promo (misal admin menaikkan batas_penggunaan), auto nonaktif bila kuota sudah habis
        $this->promoService()->nonaktifkanJikaHabis($promo);

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo "' . $promo->nama_promo . '" berhasil diperbarui.');
    }

    public function destroy($id) {
        // Ambil promo yang mau dihapus
        $promo = Promo::findOrFail($id);
        $namaPromo = $promo->nama_promo;

        // Promo yang sudah pernah dipakai tidak boleh dihapus (FK pesanan_promo),
        // jadi promo-nya dinonaktifkan supaya riwayat pesanan tetap utuh.
        // Dicek dari jumlah_terpakai (sumber pencatat kuota) DAN relasi pesanan,
        // supaya promo yang somehow terpakai tapi kuotanya nol tetap aman.
        if ($promo->jumlah_terpakai > 0 || $promo->pesanan()->exists()) {
            $promo->update(['status_aktif' => false]);

            return redirect()->route('admin.promo.index')
                ->with('error', 'Promo "' . $namaPromo . '" sudah pernah dipakai, jadi dinonaktifkan (tidak dihapus).');
        }

        // Hapus promo dari database (hard delete)
        $promo->delete();

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo "' . $namaPromo . '" berhasil dihapus.');
    }

    // Logika auto-nonaktif kuota tinggal di PromoService (satu sumber kebenaran),
    // dipakai juga kasir & checkout customer.
    private function promoService(): PromoService
    {
        return app(PromoService::class);
    }

    // Aturan validasi form promo, dipakai bareng oleh store() & update()
    private function rulesValidasi(Request $request): array
    {
        return [
            'nama_promo' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'tipe_potongan' => ['required', Rule::in(['nominal', 'persen', 'gratis_ongkir'])],
            // Nilai potongan wajib diisi kecuali promo bertipe gratis ongkir
            'nilai_potongan' => [
                'required_if:tipe_potongan,nominal,persen',
                'nullable',
                'numeric',
                'min:0',
                // Promo persen tidak boleh di atas 100% (lebih besar dari subtotal)
                Rule::when($request->input('tipe_potongan') === 'persen', ['max:100']),
            ],
            'syarat_min_porsi' => ['nullable', 'integer', 'min:1'],
            'batas_penggunaan' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status_aktif' => ['nullable', 'boolean'],
        ];
    }
}
