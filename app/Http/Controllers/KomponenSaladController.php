<?php


namespace App\Http\Controllers;


use App\Models\KomponenSalad;


class KomponenSaladController extends Controller
{
    public function index() {
        // Ambil semua komponen salad yang sedang aktif (bisa dipilih customer)
        $komponen = KomponenSalad::where('status_aktif', true)
            ->get();

        // Pisahkan komponen per jenis agar Blade bisa looping 2 section terpisah
        $sayuran = $komponen->where('jenis', 'sayuran')->values();
        $protein = $komponen->where('jenis', 'protein')->values();

        // Kirim kedua section ke view supaya Blade bisa looping terpisah
        return view('kreasikan-salad', [
            'sayuran' => $sayuran,
            'protein' => $protein,
        ]);
    }
}
