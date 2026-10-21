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
        Schema::create('detail_pesanan_komponen', function (Blueprint $table) {
            // Relasi ke item detail pesanan custom salad
            $table->foreignId('id_detail')->constrained('detail_pesanan', 'id_detail')->onDelete('cascade');

            // Relasi ke komponen salad yang dipilih customer
            $table->foreignId('id_komponen')->constrained('komponen_salad', 'id_komponen');

            // Snapshot biaya tambahan komponen saat transaksi
            $table->decimal('harga_tambahan', 10, 2)->default(0)->comment('snapshot harga_tambahan komponen saat transaksi');

            // Composite primary key item detail pesanan dan komponen salad
            $table->primary(['id_detail', 'id_komponen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan_komponen');
    }
};
