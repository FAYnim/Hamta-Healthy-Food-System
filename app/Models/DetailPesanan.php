<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';

    protected $primaryKey = 'id_detail';

    // Hanya created_at yang ada di tabel ini (tidak ada updated_at)
    const UPDATED_AT = null;

    protected $fillable = [
        'id_pesanan',
        'id_produk',
        'nama_item',
        'jumlah',
        'harga_satuan',
        'subtotal_item',
        'status_item',
    ];

    // Detail item ini bagian dari satu pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Detail item ini merujuk ke satu produk reguler (NULL jika item custom salad)
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    // Item custom salad ini terdiri dari banyak komponen (lewat junction detail_pesanan_komponen)
    public function komponenSalad()
    {
        return $this->belongsToMany(
            KomponenSalad::class,
            'detail_pesanan_komponen',
            'id_detail',
            'id_komponen'
        )->withPivot('harga_tambahan');
    }
}
