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
    ];
}
