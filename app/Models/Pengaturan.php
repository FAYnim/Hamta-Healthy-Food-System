<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $primaryKey = 'id_pengaturan';

    protected $fillable = [
        'logo',
        'whatsapp',
        'instagram',
        'tarif_ongkir',
    ];

    protected $casts = [
        'tarif_ongkir' => 'float',
    ];

    public static function getTarifOngkir(): float
    {
        return (float) (self::value('tarif_ongkir') ?? 2000.00);
    }
}
