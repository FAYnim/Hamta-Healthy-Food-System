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
        Schema::create('komponen_salad', function (Blueprint $table) {
            // Primary key komponen salad
            $table->id('id_komponen');

            // Nama komponen pilihan salad custom (misal: Selada Romaine, Dada Ayam Panggang)
            $table->string('nama_komponen', 100);

            // Kategori jenis komponen salad (sayuran atau protein)
            $table->enum('jenis', ['sayuran', 'protein']);

            // Biaya tambahan jika komponen ini dipilih
            $table->decimal('harga_tambahan', 10, 2)->default(0);

            // Path/nama file foto komponen
            $table->string('foto', 255)->nullable();

            // Deskripsi singkat mengenai komponen salad
            $table->string('deskripsi', 255)->nullable();

            // Status aktif/ketersediaan komponen salad
            $table->boolean('status_aktif')->default(true);

            // Waktu data komponen dibuat dan diperbarui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komponen_salad');
    }
};
