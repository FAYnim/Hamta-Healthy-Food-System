<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\KomponenSalad;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $tab = in_array($request->tab, ['produk', 'komponen', 'bahan'], true)
            ? $request->tab
            : 'produk';

        // Whitelist, bukan validate(): parameter GET yang salah cukup diabaikan,
        // tidak boleh melempar halaman error 422 ke admin.
        $kategori = in_array($request->kategori, ['Salad', 'Sandwich', 'Juice'], true)
            ? $request->kategori
            : null;

        // is_string() menjaga input `?cari[]=x` (array) tidak memicu TypeError
        $cari = is_string($request->cari) ? trim($request->cari) : '';
        $cari = $cari !== '' ? $cari : null;

        // Tampilkan semua produk, termasuk yang nonaktif (status_aktif = false),
        // supaya admin bisa melihat & mengaktifkan kembali produk yang sold-out
        $produk = Produk::with(['cabang', 'bahan'])
            ->when($kategori, fn ($q, $k) => $q->where('kategori', $k))
            ->when($cari !== null, fn ($q) => $q->where('nama_produk', 'like', "%{$cari}%"))
            ->orderBy('nama_produk')
            ->paginate(10)
            ->withQueryString();

        $komponen = KomponenSalad::with('bahan')
            ->orderBy('jenis')
            ->orderBy('nama_komponen')
            ->get();

        $bahan = Bahan::orderBy('nama_bahan')->get();

        return view('admin.kelola_menu', [
            'produk' => $produk,
            'kategori' => $kategori,
            'cari' => $cari,
            'tab' => $tab,
            'komponen' => $komponen,
            'bahan' => $bahan,
        ]);
    }

    public function create()
    {
        // Tampilkan form tambah produk baru
        return view('admin.menu.create');
    }

    public function store(Request $request)
    {
        // Validasi input form tambah produk.
        // Form cuma punya 1 field Harga → dipakai utk harga_offline & harga_online
        $data = $request->validate([
            'nama_produk' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'kategori' => ['required', 'in:Salad,Sandwich,Juice'],
            'harga' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'id_cabang' => ['nullable', 'integer', 'exists:cabang,id_cabang'],
            'status_aktif' => ['nullable', 'boolean'],
            'bisa_dikustom' => ['nullable', 'boolean'],
        ]);

        // Simpan produk baru ke database
        Produk::create([
            'nama_produk' => $data['nama_produk'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'kategori' => $data['kategori'],
            'harga_offline' => $data['harga'],
            'harga_online' => $data['harga'],
            // Foto diupload bareng form, simpan path-nya di disk public
            'foto' => $request->hasFile('foto')
                ? $request->file('foto')->store('produk', 'public')
                : null,
            // NULL = produk tersedia di semua cabang
            'id_cabang' => $data['id_cabang'] ?? null,
            'status_aktif' => $data['status_aktif'] ?? true,
            'bisa_dikustom' => $request->boolean('bisa_dikustom'),
        ]);

        return redirect()->route('admin.kelola_menu')
            ->with('success', 'Menu "'.$data['nama_produk'].'" berhasil ditambahkan.');
    }

    public function edit($id)
    {
        // Ambil 1 produk untuk diisi ke form edit
        $produk = Produk::findOrFail($id);

        return view('admin.menu.edit', ['produk' => $produk]);
    }

    public function update(Request $request, $id)
    {
        // Form edit punya 1 field Harga → dipakai utk harga_offline & harga_online
        $data = $request->validate([
            'nama_produk' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'kategori' => ['required', 'in:Salad,Sandwich,Juice'],
            'harga' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'bisa_dikustom' => ['nullable', 'boolean'],
        ]);

        $produk = Produk::findOrFail($id);

        // Foto lama dipertahankan kecuali admin upload foto baru
        $fotoPath = $produk->foto;
        if ($request->hasFile('foto')) {
            if ($produk->foto) {
                Storage::disk('public')->delete($produk->foto);
            }
            $fotoPath = $request->file('foto')->store('produk', 'public');
        }

        $produk->update([
            'nama_produk' => $data['nama_produk'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'kategori' => $data['kategori'],
            'harga_offline' => $data['harga'],
            'harga_online' => $data['harga'],
            'foto' => $fotoPath,
            'bisa_dikustom' => $request->boolean('bisa_dikustom'),
        ]);

        return redirect()->route('admin.kelola_menu')
            ->with('success', 'Menu "'.$data['nama_produk'].'" berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        // Toggle sold-out: false = menu disembunyikan dari kasir & katalog customer
        $produk = Produk::findOrFail($id);
        $produk->update(['status_aktif' => ! $produk->status_aktif]);

        // Kembali ke daftar menu dengan filter kategori/cari yang masih aktif
        return $this->kembaliKeDaftarMenu()->with(
            'success',
            'Menu "'.$produk->nama_produk.'" ditandai '.($produk->status_aktif ? 'tersedia.' : 'sold-out.')
        );
    }

    public function destroy($id)
    {
        // Hapus produk dari database (hard delete)
        $produk = Produk::findOrFail($id);
        $nama_produk = $produk->nama_produk;

        if ($produk->foto) {
            Storage::disk('public')->delete($produk->foto);
        }

        $produk->delete();

        return $this->kembaliKeDaftarMenu()
            ->with('success', 'Menu "'.$nama_produk.'" berhasil dihapus.');
    }

    public function toggleKomponenStatus($id)
    {
        $komponen = KomponenSalad::findOrFail($id);
        $komponen->update(['status_aktif' => ! $komponen->status_aktif]);

        return redirect()->route('admin.kelola_menu', ['tab' => 'komponen'])
            ->with('success', 'Komponen "'.$komponen->nama_komponen.'" ditandai '.($komponen->status_aktif ? 'tersedia.' : 'sold-out.'));
    }

    public function updateKomponen(Request $request, $id)
    {
        $komponen = KomponenSalad::findOrFail($id);
        $data = $request->validate([
            'nama_komponen' => ['required', 'string', 'max:100'],
            'jenis' => ['required', Rule::in(['sayuran', 'protein'])],
            'harga_tambahan' => ['required', 'numeric', 'min:0'],
        ]);

        $komponen->update([
            'nama_komponen' => $data['nama_komponen'],
            'jenis' => $data['jenis'],
            'harga_tambahan' => $data['harga_tambahan'],
            'status_aktif' => $request->boolean('status_aktif'),
        ]);

        return redirect()->route('admin.kelola_menu', ['tab' => 'komponen'])
            ->with('success', 'Komponen "'.$komponen->nama_komponen.'" berhasil diperbarui.');
    }

    public function storeKomponen(Request $request)
    {
        $data = $request->validate([
            'nama_komponen' => ['required', 'string', 'max:100'],
            'jenis' => ['required', Rule::in(['sayuran', 'protein'])],
            'harga_tambahan' => ['required', 'numeric', 'min:0'],
        ]);

        KomponenSalad::create([
            'nama_komponen' => $data['nama_komponen'],
            'jenis' => $data['jenis'],
            'harga_tambahan' => $data['harga_tambahan'],
            'status_aktif' => true,
        ]);

        return redirect()->route('admin.kelola_menu', ['tab' => 'komponen'])
            ->with('success', 'Komponen salad baru "'.$data['nama_komponen'].'" berhasil ditambahkan.');
    }

    public function destroyKomponen($id)
    {
        $komponen = KomponenSalad::findOrFail($id);
        $nama = $komponen->nama_komponen;
        $komponen->delete();

        return redirect()->route('admin.kelola_menu', ['tab' => 'komponen'])
            ->with('success', 'Komponen "'.$nama.'" berhasil dihapus.');
    }

    /**
     * Redirect kembali ke URL sebelumnya hanya jika host-nya sama dengan aplikasi ini.
     * back() bawaan Laravel menerima Referer lintas-host tanpa validasi (open redirect),
     * jadi host dicek sendiri dan fallback ke daftar menu admin.
     */
    private function kembaliKeDaftarMenu()
    {
        $previous = url()->previous();
        $host = parse_url($previous, PHP_URL_HOST);

        $tujuan = $host === request()->getHost()
            ? $previous
            : route('admin.kelola_menu');

        return redirect()->to($tujuan);
    }
}
