<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Promo;


class PromoController extends \App\Http\Controllers\Controller
{
    public function index() {
        // Tampilkan semua promo (aktif & nonaktif) supaya admin bisa monitored & diaktifkan kembali
        $promo = Promo::orderBy('nama_promo')->get();

        return view('admin.promo.index', ['promo' => $promo]);
    }

    public function create() {
        // Tampilkan form tambah promo baru
        return view('admin.promo.create');
    }

    public function store(Request $request) {
        // Validasi input form tambah promo
        $data = $request->validate([
            'nama_promo' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'tipe_potongan' => ['required', Rule::in(['nominal', 'persen', 'gratis_ongkir'])],
            // Nilai potongan wajib diisi kecuali promo bertipe gratis ongkir
            'nilai_potongan' => ['required_if:tipe_potongan,nominal,persen', 'nullable', 'numeric', 'min:0'],
            'syarat_min_porsi' => ['nullable', 'integer', 'min:1'],
            'batas_penggunaan' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

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

    public function edit($id) {
        // Ambil 1 promo untuk diisi ke form edit
        $promo = Promo::findOrFail($id);

        return view('admin.promo.edit', ['promo' => $promo]);
    }

    public function update(Request $request, $id) {
        // Validasi input form edit promo (sama persis dengan form tambah)
        $data = $request->validate([
            'nama_promo' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'tipe_potongan' => ['required', Rule::in(['nominal', 'persen', 'gratis_ongkir'])],
            'nilai_potongan' => ['required_if:tipe_potongan,nominal,persen', 'nullable', 'numeric', 'min:0'],
            'syarat_min_porsi' => ['nullable', 'integer', 'min:1'],
            'batas_penggunaan' => ['required', 'integer', 'min:1'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        // Update data promo yang dipilih (jumlah_terpakai tidak diedit, itu diurus sistem)
        $promo = Promo::findOrFail($id);
        $promo->update([
            'nama_promo' => $data['nama_promo'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tipe_potongan' => $data['tipe_potongan'],
            'nilai_potongan' => $data['tipe_potongan'] === 'gratis_ongkir' ? null : $data['nilai_potongan'],
            'syarat_min_porsi' => $data['syarat_min_porsi'] ?? null,
            'batas_penggunaan' => $data['batas_penggunaan'],
            'status_aktif' => $data['status_aktif'] ?? false,
            'tanggal_mulai' => $data['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $data['tanggal_selesai'] ?? null,
        ]);

        // Cek lagi kuota promo (misal admin menaikkan batas_penggunaan), auto nonaktif bila kuota sudah habis
        $this->nonaktifkanOtomatis($promo);

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo "' . $promo->nama_promo . '" berhasil diperbarui.');
    }

    public function destroy($id) {
        // Ambil promo yang mau dihapus
        $promo = Promo::findOrFail($id);
        $nama_promo = $promo->nama_promo;

        // Promo yang sudah pernah dipakai tidak boleh dihapus (FK pesanan_promo),
        // jadi promo-nya dinonaktifkan supaya riwayat pesanan tetap utuh
        if ($promo->pesanan()->exists()) {
            $promo->update(['status_aktif' => false]);

            return redirect()->route('admin.promo.index')
                ->with('error', 'Promo "' . $nama_promo . '" sudah pernah dipakai, jadi dinonaktifkan (tidak dihapus).');
        }

        // Hapus promo dari database (hard delete)
        $promo->delete();

        return redirect()->route('admin.promo.index')
            ->with('success', 'Promo "' . $nama_promo . '" berhasil dihapus.');
    }

    // Nonaktifkan promo otomatis kalau kuota pakainya sudah habis.
    // Dipanggil dari update() di controller ini, tapi sebaiknya juga dipanggil
    // dari PesananController (customer) & KasirController setiap kali jumlah_terpakai promo bertambah.
    // Karena method ini private, kalau mau dipakai controller lain ubah jadi public
    // atau taruh logic-nya di model Promo supaya jadi satu sumber kebenaran.
    private function nonaktifkanOtomatis(Promo $promo) {
        if ($promo->jumlah_terpakai >= $promo->batas_penggunaan) {
            $promo->update(['status_aktif' => false]);
        }
    }
}
