<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Produk;


class MenuController extends \App\Http\Controllers\Controller
{
    public function index() {
        // Tampilkan semua produk, termasuk yang nonaktif (status_aktif = false),
        // supaya admin bisa melihat & mengaktifkan kembali produk yang sold-out
        $produk = Produk::with('cabang')->orderBy('nama_produk')->get();

        return view('admin.kelola_menu', ['produk' => $produk]);
    }

    public function create() {
        // Tampilkan form tambah produk baru
        return view('admin.menu.create');
    }

    public function store(Request $request) {
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
            'bisa_dikustom' => $data['bisa_dikustom'] ?? false,
        ]);

        return redirect()->route('admin.kelola_menu')
            ->with('success', 'Menu "' . $data['nama_produk'] . '" berhasil ditambahkan.');
    }

    public function edit($id) {
        // Ambil 1 produk untuk diisi ke form edit
        $produk = Produk::findOrFail($id);

        return view('admin.menu.edit', ['produk' => $produk]);
    }

    public function update(Request $request, $id) {
        // Form edit punya 1 field Harga → dipakai utk harga_offline & harga_online
        $data = $request->validate([
            'nama_produk' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'kategori' => ['required', 'in:Salad,Sandwich,Juice'],
            'harga' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'image', 'max:2048'],
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
        ]);

        return redirect()->route('admin.kelola_menu')
            ->with('success', 'Menu "' . $data['nama_produk'] . '" berhasil diperbarui.');
    }

    public function destroy($id) {
        // Hapus produk dari database (hard delete)
        $produk = Produk::findOrFail($id);
        $nama_produk = $produk->nama_produk;

        if ($produk->foto) {
            Storage::disk('public')->delete($produk->foto);
        }

        $produk->delete();

        return redirect()->route('admin.kelola_menu')
            ->with('success', 'Menu "' . $nama_produk . '" berhasil dihapus.');
    }
}
