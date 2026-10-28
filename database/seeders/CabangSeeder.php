<?php

namespace Database\Seeders;

use App\Models\Cabang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cabang::create([
            'nama_cabang'    => 'Lidah Kulon',
            'alamat'         => 'Jl. Lidah Kulon Gg. I, Lakarsantri',
            'jam_buka'       => '09:00',
            'jam_tutup'      => '21:00',
            'layani_pickup'  => true,
            'layani_dinein'  => false,
        ]);

        Cabang::create([
            'nama_cabang'    => 'MERR (Apartemen Bale Hinggil)',
            'alamat'         => 'Bale Hinggil Apartment (Jl. Merr / Ir. H. Soekarno), Surabaya', // placeholder -- alamat lengkap belum didapat dari wawancara
            'jam_buka'       => '09:00',
            'jam_tutup'      => '21:00',
            'layani_pickup'  => true,
            'layani_dinein'  => true,
        ]);
    }
}
