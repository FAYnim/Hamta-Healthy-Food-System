<?php

namespace App\Models;

use Carbon\Carbon;
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
        'midtrans_order_id',
        'midtrans_response',
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
        'midtrans_response' => 'array',
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

    /**
     * Cek apakah pesanan QRIS Midtrans ini sudah melewati batas waktu pembayaran.
     * Jika sudah lewat dan belum lunas, otomatis ubah status pesanan menjadi 'batal'
     * dan status pembayaran menjadi 'expired'.
     */
    public function batalkanJikaExpired(): bool
    {
        if ($this->status_pesanan === 'batal' && $this->status_pembayaran === 'expired') {
            return false;
        }

        if (! in_array($this->status_pembayaran, ['pending', 'menunggu_verifikasi'], true)) {
            return false;
        }

        if (! in_array($this->metode_bayar, ['midtrans', 'qris'], true)) {
            return false;
        }

        $expiryTime = $this->midtrans_response['expiry_time'] ?? null;
        if (! $expiryTime) {
            return false;
        }

        try {
            if (now()->greaterThanOrEqualTo(Carbon::parse($expiryTime))) {
                $this->update([
                    'status_pembayaran' => 'expired',
                    'status_pesanan' => 'batal',
                ]);
                $this->refresh();

                return true;
            }
        } catch (\Throwable) {
            return false;
        }

        return false;
    }

    /**
     * Sinkronkan dan batalkan seluruh pesanan QRIS Midtrans yang telah melewati batas waktu kedaluwarsa.
     */
    public static function sinkronkanPesananExpired(): int
    {
        $pesananPending = static::whereIn('status_pembayaran', ['pending', 'menunggu_verifikasi'])
            ->whereIn('metode_bayar', ['midtrans', 'qris'])
            ->where('status_pesanan', '!=', 'batal')
            ->whereNotNull('midtrans_response')
            ->get();

        $jumlah = 0;
        foreach ($pesananPending as $p) {
            if ($p->batalkanJikaExpired()) {
                $jumlah++;
            }
        }

        return $jumlah;
    }
}
