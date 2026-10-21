<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabel junction produk <-> bahan (composite primary key: id_produk + id_bahan).
 *
 * PERHATIAN: Eloquent tidak mendukung composite primary key secara native.
 * Operasi find() dan save() butuh penanganan manual jika dipakai langsung
 * (misalnya via query builder atau override method find).
 * Model ini tetap berguna untuk query builder & scope biasa.
 */
class ProdukBahan extends Model
{
    protected $table = 'produk_bahan';

    // Composite primary key — tidak set $primaryKey tunggal.
    // Lihat komentar di atas untuk keterbatasan Eloquent.
    public $incrementing = false;

    // Junction table ini tidak punya timestamps
    public $timestamps = false;

    protected $fillable = [
        'id_produk',
        'id_bahan',
    ];

    // Baris ini merepresentasikan satu produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    // Baris ini merepresentasikan satu bahan
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'id_bahan', 'id_bahan');
    }
}
