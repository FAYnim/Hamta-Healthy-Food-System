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
        Schema::create('komponen_bahan', function (Blueprint $table) {
            // Relasi ke komponen salad custom
            $table->foreignId('id_komponen')->constrained('komponen_salad', 'id_komponen')->onDelete('cascade');

            // Relasi ke bahan yang menjadi komposisi komponen tersebut
            $table->foreignId('id_bahan')->constrained('bahan', 'id_bahan')->onDelete('cascade');

            // Composite primary key pasangan komponen salad dan bahan
            $table->primary(['id_komponen', 'id_bahan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komponen_bahan');
    }
};
