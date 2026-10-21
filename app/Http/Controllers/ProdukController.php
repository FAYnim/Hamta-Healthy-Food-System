<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class ProdukController extends Controller
{
    public function index() {
        $produk = Produk::where('status_aktif', true)
            ->with('cabang')
            ->get();
        return view('katalog', ['produk' => $produk]);
    }


    public function show($id) {
        $produk = Produk::with(['cabang', 'bahan'])
            ->findOrFail($id);
        
        return view('produk-detail', ['produk' => $produk]);
    }
}
