<?php


namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Models\Produk;


class MenuController extends \App\Http\Controllers\Controller
{
    public function index() {
        // Tampilkan semua produk, termasuk yang nonaktif (status_aktif = false),
        // supaya admin bisa melihat & mengaktifkan kembali produk yang sold-out
        $produk = Produk::with('cabang')->orderBy('nama_produk')->get();

        return view('admin.menu.index', ['produk' => $produk]);
    }

    public function create() {
        // Tampilkan form tambah produk baru
        return view('admin.menu.create');
    }

    public function store(Request $request) {
        // Validasi input form tambah produk
        $data = $request->validate([
            'nama_produk' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'harga_offline' => ['required', 'numeric', 'min:0'],
            'harga_online' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'string', 'max:255'],
            'id_cabang' => ['nullable', 'integer', 'exists:cabang,id_cabang'],
            'status_aktif' => ['nullable', 'boolean'],
            'bisa_dikustom' => ['nullable', 'boolean'],
        ]);

        // Simpan produk baru ke database
        Produk::create([
            'nama_produk' => $data['nama_produk'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'harga_offline' => $data['harga_offline'],
            'harga_online' => $data['harga_online'],
            // Foto sudah diupload terpisah, di sini cukup disimpan path-nya
            'foto' => $data['foto'] ?? null,
            // NULL = produk tersedia di semua cabang
            'id_cabang' => $data['id_cabang'] ?? null,
            'status_aktif' => $data['status_aktif'] ?? true,
            'bisa_dikustom' => $data['bisa_dikustom'] ?? false,
        ]);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Produk "' . $data['nama_produk'] . '" berhasil ditambahkan.');
    }

    public function edit($id) {
        // Ambil 1 produk untuk diisi ke form edit
        $produk = Produk::findOrFail($id);

        return view('admin.menu.edit', ['produk' => $produk]);
    }

    public function update(Request $request, $id) {
        // Validasi input form edit produk
        $data = $request->validate([
            'nama_produk' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'harga_offline' => ['required', 'numeric', 'min:0'],
            'harga_online' => ['required', 'numeric', 'min:0'],
            'foto' => ['nullable', 'string', 'max:255'],
            'id_cabang' => ['nullable', 'integer', 'exists:cabang,id_cabang'],
            'status_aktif' => ['nullable', 'boolean'],
            'bisa_dikustom' => ['nullable', 'boolean'],
        ]);

        // Update data produk yang dipilih
        $produk = Produk::findOrFail($id);
        $produk->update([
            'nama_produk' => $data['nama_produk'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'harga_offline' => $data['harga_offline'],
            'harga_online' => $data['harga_online'],
            'foto' => $data['foto'] ?? null,
            'id_cabang' => $data['id_cabang'] ?? null,
            'status_aktif' => $data['status_aktif'] ?? false,
            'bisa_dikustom' => $data['bisa_dikustom'] ?? false,
        ]);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Produk "' . $data['nama_produk'] . '" berhasil diperbarui.');
    }

    public function destroy($id) {
        // Hapus produk dari database (hard delete)
        $produk = Produk::findOrFail($id);
        $nama_produk = $produk->nama_produk;
        $produk->delete();

        return redirect()->route('admin.menu.index')
            ->with('success', 'Produk "' . $nama_produk . '" berhasil dihapus.');
    }
}
