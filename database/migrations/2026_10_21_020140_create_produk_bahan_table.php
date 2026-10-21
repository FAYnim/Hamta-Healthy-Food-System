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
        Schema::create('produk_bahan', function (Blueprint $table) {
            // Relasi ke produk yang memiliki komposisi bahan ini
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->onDelete('cascade');

            // Relasi ke bahan yang menjadi bagian dari produk
            $table->foreignId('id_bahan')->constrained('bahan', 'id_bahan')->onDelete('cascade');

            // Composite primary key pasangan produk dan bahan
            $table->primary(['id_produk', 'id_bahan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_bahan');
    }
};
