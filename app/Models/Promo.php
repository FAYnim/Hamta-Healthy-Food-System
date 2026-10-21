<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'promo';

    protected $primaryKey = 'id_promo';

    protected $fillable = [
        'nama_promo',
        'deskripsi',
        'tipe_potongan',
        'nilai_potongan',
        'syarat_min_porsi',
        'batas_penggunaan',
        'jumlah_terpakai',
        'status_aktif',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    // Satu promo bisa diterapkan ke banyak pesanan (lewat junction pesanan_promo)
    public function pesanan()
    {
        return $this->belongsToMany(
            Pesanan::class,
            'pesanan_promo',
            'id_promo',
            'id_pesanan'
        )->withPivot('nilai_potongan');
    }
}
