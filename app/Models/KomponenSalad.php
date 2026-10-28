<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomponenSalad extends Model
{
    protected $table = 'komponen_salad';

    protected $primaryKey = 'id_komponen';

    protected $fillable = [
        'nama_komponen',
        'jenis',
        'harga_tambahan',
        'foto',
        'deskripsi',
        'status_aktif',
    ];

    // Satu komponen salad bisa terdiri dari banyak bahan (lewat junction komponen_bahan)
    public function bahan()
    {
        return $this->belongsToMany(
            Bahan::class,
            'komponen_bahan',
            'id_komponen',
            'id_bahan'
        );
    }

    // Satu komponen salad bisa dipilih di banyak detail pesanan custom (lewat junction detail_pesanan_komponen)
    public function detailPesanan()
    {
        return $this->belongsToMany(
            DetailPesanan::class,
            'detail_pesanan_komponen',
            'id_komponen',
            'id_detail'
        )->withPivot('harga_tambahan');
    }
}
