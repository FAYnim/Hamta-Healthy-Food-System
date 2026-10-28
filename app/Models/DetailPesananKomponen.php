<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabel junction detail_pesanan <-> komponen_salad
 * (composite primary key: id_detail + id_komponen).
 *
 * PERHATIAN: Eloquent tidak mendukung composite primary key secara native.
 * Operasi find() dan save() butuh penanganan manual jika dipakai langsung
 * (misalnya via query builder atau override method find).
 * Model ini tetap berguna untuk query builder & scope biasa.
 */
class DetailPesananKomponen extends Model
{
    protected $table = 'detail_pesanan_komponen';

    // Composite primary key — tidak set $primaryKey tunggal.
    // Lihat komentar di atas untuk keterbatasan Eloquent.
    public $incrementing = false;

    // Junction table ini tidak punya timestamps
    public $timestamps = false;

    protected $fillable = [
        'id_detail',
        'id_komponen',
        'harga_tambahan',
    ];

    // Baris ini merepresentasikan satu item detail pesanan (custom salad)
    public function detailPesanan()
    {
        return $this->belongsTo(DetailPesanan::class, 'id_detail', 'id_detail');
    }

    // Baris ini merepresentasikan satu komponen salad yang dipilih
    public function komponenSalad()
    {
        return $this->belongsTo(KomponenSalad::class, 'id_komponen', 'id_komponen');
    }
}
