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
        Schema::create('promo', function (Blueprint $table) {
            // Primary key promo
            $table->id('id_promo');

            // Nama kupon / diskon promosi
            $table->string('nama_promo', 100);

            // Deskripsi detail ketentuan promo
            $table->string('deskripsi', 255)->nullable();

            // Tipe diskon: nominal tetap, persentase, atau gratis ongkos kirim
            $table->enum('tipe_potongan', ['nominal', 'persen', 'gratis_ongkir']);

            // Nilai potongan dalam Rp atau persen (NULL jika gratis ongkir)
            $table->decimal('nilai_potongan', 10, 2)->nullable()->comment('nominal Rp atau persen, NULL jika gratis_ongkir');

            // Syarat minimum porsi pesanan agar promo dapat digunakan
            $table->unsignedInteger('syarat_min_porsi')->nullable()->comment('kondisi custom, misal >20 porsi');

            // Kuota batas maksimum customer yang dapat menggunakan promo
            $table->unsignedInteger('batas_penggunaan')->comment('maksimal jumlah customer yg bisa pakai');

            // Jumlah transaksi yang telah berhasil menggunakan promo ini
            $table->unsignedInteger('jumlah_terpakai')->default(0);

            // Status keaktifan promo (nonaktif otomatis jika kuota habis)
            $table->boolean('status_aktif')->default(true)->comment('auto nonaktif saat jumlah_terpakai >= batas_penggunaan');

            // Tanggal awal berlakunya periode promo
            $table->date('tanggal_mulai')->nullable();

            // Tanggal akhir berlakunya periode promo
            $table->date('tanggal_selesai')->nullable();

            // Waktu data promo dibuat dan diperbarui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo');
    }
};
