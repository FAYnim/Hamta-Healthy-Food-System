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
        Schema::create('cabang', function (Blueprint $table) {
            // Primary key cabang
            $table->id('id_cabang');

            // Nama cabang toko
            $table->string('nama_cabang', 100);

            // Alamat fisik cabang toko
            $table->string('alamat', 255);

            // Jam operasional buka cabang
            $table->time('jam_buka');

            // Jam operasional tutup cabang
            $table->time('jam_tutup');

            // Status apakah cabang melayani pesanan pickup
            $table->boolean('layani_pickup')->default(true);

            // Status apakah cabang melayani dine-in (makan di tempat)
            $table->boolean('layani_dinein')->default(false);

            // Waktu data cabang dibuat dan diperbarui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabang');
    }
};
