<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabel junction komponen_salad <-> bahan (composite primary key: id_komponen + id_bahan).
 *
 * PERHATIAN: Eloquent tidak mendukung composite primary key secara native.
 * Operasi find() dan save() butuh penanganan manual jika dipakai langsung
 * (misalnya via query builder atau override method find).
 * Model ini tetap berguna untuk query builder & scope biasa.
 */
class KomponenBahan extends Model
{
    protected $table = 'komponen_bahan';

    // Composite primary key — tidak set $primaryKey tunggal.
    // Lihat komentar di atas untuk keterbatasan Eloquent.
    public $incrementing = false;

    // Junction table ini tidak punya timestamps
    public $timestamps = false;

    protected $fillable = [
        'id_komponen',
        'id_bahan',
    ];

    // Baris ini merepresentasikan satu komponen salad
    public function komponenSalad()
    {
        return $this->belongsTo(KomponenSalad::class, 'id_komponen', 'id_komponen');
    }

    // Baris ini merepresentasikan satu bahan
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'id_bahan', 'id_bahan');
    }
}
