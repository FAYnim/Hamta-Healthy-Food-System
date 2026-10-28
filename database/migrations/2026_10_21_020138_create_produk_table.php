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
        Schema::create('produk', function (Blueprint $table) {
            // Primary key produk
            $table->id('id_produk');

            // Nama produk menu reguler
            $table->string('nama_produk', 100);

            // Deskripsi detail mengenai produk
            $table->text('deskripsi')->nullable();

            // Kategori menu utk tab filter: Salad / Sandwich / Juice
            $table->enum('kategori', ['Salad', 'Sandwich', 'Juice'])->notNull();

            // Harga jual produk saat pembelian offline/di kasir
            $table->decimal('harga_offline', 10, 2);

            // Harga jual produk saat pemesanan online via website
            $table->decimal('harga_online', 10, 2)->comment('harga online dibuat lebih tinggi dari offline');

            // Path/nama file foto produk
            $table->string('foto', 255)->nullable();

            // Status aktif/ketersediaan produk (toggle sold-out manual)
            $table->boolean('status_aktif')->default(true)->comment('toggle sold-out manual');

            // Status apakah produk bisa dikustomisasi komposisinya
            $table->boolean('bisa_dikustom')->default(false)->comment("false utk Chef's Salad (komposisi tetap)");

            // Relasi ke cabang: NULL = tersedia di semua cabang; diisi jika eksklusif cabang tertentu
            $table->foreignId('id_cabang')
                ->nullable()
                ->comment('NULL = tersedia di semua cabang; diisi jika eksklusif cabang tertentu (misal Rice Bowl = MERR)')
                ->constrained('cabang', 'id_cabang')
                ->onDelete('set null');

            // Waktu data produk dibuat dan diperbarui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
