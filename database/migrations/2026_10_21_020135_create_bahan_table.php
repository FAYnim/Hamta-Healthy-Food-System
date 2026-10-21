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
        Schema::create('bahan', function (Blueprint $table) {
            // Primary key bahan
            $table->id('id_bahan');

            // Nama bahan atau ingredient
            $table->string('nama_bahan', 100);

            // Keterangan tambahan bahan (misal info alergen/komposisi)
            $table->string('keterangan', 255)->nullable()->comment('info alergen/komposisi tambahan');

            // Waktu data bahan dibuat dan diperbarui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan');
    }
};
