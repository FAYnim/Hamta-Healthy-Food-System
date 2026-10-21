<?php

namespace Database\Seeders;

use App\Models\Bahan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftar_bahan = [
            // Sayuran
            ['nama_bahan' => 'Selada'],
            ['nama_bahan' => 'Romaine'],
            ['nama_bahan' => 'Kol Ungu'],
            ['nama_bahan' => 'Tomat'],
            ['nama_bahan' => 'Timun'],
            ['nama_bahan' => 'Kacang Polong'],
            ['nama_bahan' => 'Mixed Vegetables'],
            // Protein
            ['nama_bahan' => 'Chicken Patties'],
            ['nama_bahan' => 'Beef Patties'],
            ['nama_bahan' => 'Grilled Chicken'],
            ['nama_bahan' => 'Telur'],
        ];

        foreach ($daftar_bahan as $bahan) {
            Bahan::create([
                'nama_bahan' => $bahan['nama_bahan'],
                'keterangan' => null, // TODO: Isi info alergen per bahan setelah konfirmasi ke owner (Kak Yunita)
            ]);
        }
    }
}
