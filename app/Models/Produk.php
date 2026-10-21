<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga_offline',
        'harga_online',
        'foto',
        'status_aktif',
        'bisa_dikustom',
        'id_cabang',
    ];

    // Produk ini milik satu cabang tertentu (NULL = berlaku di semua cabang)
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }

    // Satu produk bisa punya banyak bahan (lewat junction produk_bahan)
    public function bahan()
    {
        return $this->belongsToMany(
            Bahan::class,
            'produk_bahan',
            'id_produk',
            'id_bahan'
        );
    }

    // Satu produk bisa muncul di banyak detail pesanan
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk', 'id_produk');
    }
}
