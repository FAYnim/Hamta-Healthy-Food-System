<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabel junction pesanan <-> promo (composite primary key: id_pesanan + id_promo).
 *
 * PERHATIAN: Eloquent tidak mendukung composite primary key secara native.
 * Operasi find() dan save() butuh penanganan manual jika dipakai langsung
 * (misalnya via query builder atau override method find).
 * Model ini tetap berguna untuk query builder & scope biasa.
 */
class PesananPromo extends Model
{
    protected $table = 'pesanan_promo';

    // Composite primary key — tidak set $primaryKey tunggal.
    // Lihat komentar di atas untuk keterbatasan Eloquent.
    public $incrementing = false;

    // Junction table ini tidak punya timestamps
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'id_promo',
        'nilai_potongan',
    ];

    // Baris ini merepresentasikan satu pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Baris ini merepresentasikan satu promo yang diterapkan
    public function promo()
    {
        return $this->belongsTo(Promo::class, 'id_promo', 'id_promo');
    }
}
