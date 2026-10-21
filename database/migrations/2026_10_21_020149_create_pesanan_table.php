<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            // Primary key pesanan
            $table->id('id_pesanan');

            // Kode unik referensi pesanan (misal: HMT-20261021-001)
            $table->string('kode_pesanan', 20)->unique();

            // Asal pesanan: online via website atau kasir POS offline
            $table->enum('jenis', ['online', 'kasir']);

            // Nama pembeli (boleh null untuk transaksi kasir walk-in tanpa identitas)
            $table->string('nama_pelanggan', 100)->nullable()->comment('NULL diperbolehkan utk transaksi kasir walk-in');

            // Nomor WhatsApp aktif pelanggan untuk notifikasi/konfirmasi
            $table->string('no_whatsapp', 20)->nullable();

            // Metode pengambilan pesanan: pickup sendiri atau delivery
            $table->enum('metode_ambil', ['pickup', 'delivery'])->default('pickup');

            // Relasi ke cabang tempat pickup atau dapur produksi yang memproses
            $table->foreignId('id_cabang')->comment('cabang pickup / cabang asal produksi')->constrained('cabang', 'id_cabang');

            // Alamat tujuan pengantaran jika metode delivery
            $table->string('alamat_delivery', 255)->nullable();

            // Biaya ongkos kirim pesanan delivery
            $table->decimal('ongkir', 10, 2)->nullable()->comment('NULL = menunggu konfirmasi admin via WA');

            // Status penentuan tarif ongkir oleh admin
            $table->enum('status_ongkir', ['tidak_perlu', 'menunggu_konfirmasi', 'terkonfirmasi'])->default('tidak_perlu');

            // Subtotal harga semua item sebelum diskon dan ongkir
            $table->decimal('subtotal', 10, 2);

            // Total potongan harga yang diperoleh dari promo
            $table->decimal('total_diskon', 10, 2)->default(0);

            // Total tagihan akhir yang wajib dibayarkan pelanggan
            $table->decimal('total_bayar', 10, 2)->comment('dihitung di backend untuk validasi');

            // Metode pembayaran: QRIS statis atau tunai di kasir
            $table->enum('metode_bayar', ['qris', 'tunai'])->default('qris');

            // File path penyimpanan bukti transfer/pembayaran QRIS
            $table->string('bukti_bayar', 255)->nullable()->comment('path upload bukti bayar QRIS');

            // Status verifikasi pembayaran oleh kasir/admin
            $table->enum('status_pembayaran', ['menunggu_verifikasi', 'terverifikasi', 'ditolak'])->default('menunggu_verifikasi');

            // Status alur pengerjaan pesanan
            $table->enum('status_pesanan', ['diproses', 'siap', 'selesai', 'batal'])->default('diproses');

            // Relasi ke admin/kasir yang menginput pesanan jika transaksi offline
            $table->foreignId('id_admin')
                ->nullable()
                ->comment('admin/kasir yang input transaksi offline, NULL utk pesanan online')
                ->constrained('admin', 'id_admin')
                ->onDelete('set null');

            // Waktu data pesanan dibuat dan diperbarui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
