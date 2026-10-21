<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'rating';

    protected $primaryKey = 'id_rating';

    // Hanya created_at yang ada di tabel ini (tidak ada updated_at)
    const UPDATED_AT = null;

    protected $fillable = [
        'id_pesanan',
        'bintang',
        'ulasan',
    ];

    // Rating ini diberikan untuk satu pesanan tertentu
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}
