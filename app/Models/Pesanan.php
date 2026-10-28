<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'kode_pesanan',
        'jenis',
        'nama_pelanggan',
        'no_whatsapp',
        'metode_ambil',
        'id_cabang',
        'alamat_delivery',
        'ongkir',
        'status_ongkir',
        'subtotal',
        'total_diskon',
        'total_bayar',
        'metode_bayar',
        'bukti_bayar',
        'status_pembayaran',
        'status_pesanan',
        'id_admin',
    ];

    // Cast kolom nominal ke decimal & kolom angka ke integer, supaya arithmetic
    // (diskon, ongkir, total) tidak salah karena string dari database
    protected $casts = [
        'ongkir' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'id_cabang' => 'integer',
        'id_admin' => 'integer',
    ];

    // Pesanan ini dilayani oleh satu cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }

    // Pesanan kasir ini diinput oleh satu admin (NULL untuk pesanan online)
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    // Satu pesanan punya banyak item detail
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Satu pesanan bisa pakai banyak promo (lewat junction pesanan_promo)
    public function promo()
    {
        return $this->belongsToMany(
            Promo::class,
            'pesanan_promo',
            'id_pesanan',
            'id_promo'
        )->withPivot('nilai_potongan');
    }

    // Satu pesanan punya satu rating (opsional)
    public function rating()
    {
        return $this->hasOne(Rating::class, 'id_pesanan', 'id_pesanan');
    }
}
