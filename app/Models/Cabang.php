<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cabang extends Model
{
    protected $table = 'cabang';

    protected $primaryKey = 'id_cabang';

    protected $fillable = [
        'nama_cabang',
        'alamat',
        'jam_buka',
        'jam_tutup',
        'jam_buka_weekend',
        'jam_tutup_weekend',
        'layani_pickup',
        'layani_dinein',
    ];

    protected function casts(): array
    {
        return [
            'layani_pickup' => 'boolean',
            'layani_dinein' => 'boolean',
        ];
    }

    public function produk(): HasMany
    {
        return $this->hasMany(Produk::class, 'id_cabang', 'id_cabang');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_cabang', 'id_cabang');
    }
}
