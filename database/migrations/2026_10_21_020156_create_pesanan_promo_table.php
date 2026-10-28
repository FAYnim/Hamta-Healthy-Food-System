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
        Schema::create('pesanan_promo', function (Blueprint $table) {
            // Relasi ke pesanan yang menerapkan promo
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->onDelete('cascade');

            // Relasi ke promo yang digunakan
            $table->foreignId('id_promo')->constrained('promo', 'id_promo');

            // Snapshot nilai nominal potongan harga promo saat transaksi
            $table->decimal('nilai_potongan', 10, 2)->comment('snapshot nilai potongan saat diterapkan');

            // Composite primary key pasangan pesanan dan promo
            $table->primary(['id_pesanan', 'id_promo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_promo');
    }
};
