<?php

namespace Database\Seeders;

use App\Models\KomponenSalad;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KomponenSaladSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sayuran (7 item)
        $sayuran = [
            'Selada',
            'Romaine',
            'Kol Ungu',
            'Tomat',
            'Timun',
            'Kacang Polong',
            'Mixed Vegetables',
        ];

        foreach ($sayuran as $nama) {
            KomponenSalad::create([
                'nama_komponen'  => $nama,
                'jenis'          => 'sayuran',
                'harga_tambahan' => 0,
                'foto'           => null,
                'deskripsi'      => null,
            ]);
        }

        // Protein (4 item)
        $protein = [
            'Chicken Patties',
            'Beef Patties',
            'Grilled Chicken',
            'Telur',
        ];

        foreach ($protein as $nama) {
            KomponenSalad::create([
                'nama_komponen'  => $nama,
                'jenis'          => 'protein',
                'harga_tambahan' => 0,    // TODO: Konfirmasi harga tambahan per komponen ke owner (Kak Yunita)
                'foto'           => null, // TODO: Upload foto komponen setelah aset tersedia
                'deskripsi'      => null,
            ]);
        }
    }
}
