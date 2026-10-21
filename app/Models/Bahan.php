<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    protected $table = 'bahan';

    protected $primaryKey = 'id_bahan';

    protected $fillable = [
        'nama_bahan',
        'keterangan',
    ];

    // Satu bahan bisa dipakai di banyak produk (lewat junction produk_bahan)
    public function produk()
    {
        return $this->belongsToMany(
            Produk::class,
            'produk_bahan',
            'id_bahan',
            'id_produk'
        );
    }

    // Satu bahan bisa dipakai di banyak komponen salad (lewat junction komponen_bahan)
    public function komponenSalad()
    {
        return $this->belongsToMany(
            KomponenSalad::class,
            'komponen_bahan',
            'id_bahan',
            'id_komponen'
        );
    }
}
