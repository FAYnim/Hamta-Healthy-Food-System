<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $table = 'cabang';

    protected $primaryKey = 'id_cabang';

    protected $fillable = [
        'nama_cabang',
        'alamat',
        'jam_buka',
        'jam_tutup',
        'layanan_pickup',
        'layanan_dinein'
    ];

    public function produk() {
        return $this->hasMany(Produk::class, 'id_cabang', 'id_cabang');
    }

    public function pesanan() {
        return $this->hasMany(Pesanan::class, 'id_cabang', 'id_cabang');
    }
}
