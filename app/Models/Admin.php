<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';

    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nama',
        'username',
        'password',
    ];

    /**
     * Sembunyikan password saat serialisasi (JSON/array).
     */
    protected $hidden = [
        'password',
    ];

    // Satu admin bisa menginput banyak pesanan kasir
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_admin', 'id_admin');
    }
}
