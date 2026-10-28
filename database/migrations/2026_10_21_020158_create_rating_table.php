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
        Schema::create('rating', function (Blueprint $table) {
            // Primary key rating ulasan
            $table->id('id_rating');

            // Relasi satu-ke-satu ke pesanan yang dinilai
            $table->foreignId('id_pesanan')->unique()->constrained('pesanan', 'id_pesanan')->onDelete('cascade');

            // Penilaian kepuasan pelanggan dalam skala bintang 1 sampai 5
            $table->unsignedTinyInteger('bintang');

            // Ulasan atau masukan teks dari pelanggan
            $table->text('ulasan')->nullable();

            // Waktu data rating ulasan dikirimkan
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rating');
    }
};
