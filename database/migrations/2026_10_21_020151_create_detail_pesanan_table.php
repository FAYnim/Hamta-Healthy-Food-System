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
        Schema::create('detail_pesanan', function (Blueprint $table) {
            // Primary key detail item pesanan
            $table->id('id_detail');

            // Relasi ke tabel header pesanan
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->onDelete('cascade');

            // Relasi ke produk reguler (NULL jika item merupakan salad custom)
            $table->foreignId('id_produk')
                ->nullable()
                ->comment('NULL jika item ini adalah custom salad')
                ->constrained('produk', 'id_produk')
                ->onDelete('set null');

            // Snapshot nama produk atau salad kustom saat transaksi berlangsung
            $table->string('nama_item', 100)->comment('snapshot nama produk/"Kreasikan Salad Sendiri" saat transaksi');

            // Kuantitas porsi item yang dipesan
            $table->unsignedInteger('jumlah')->default(1);

            // Snapshot harga satuan item saat transaksi
            $table->decimal('harga_satuan', 10, 2)->comment('snapshot harga saat transaksi');

            // Subtotal harga item (jumlah dikali harga_satuan)
            $table->decimal('subtotal_item', 10, 2);

            // Status ketersediaan item jika terjadi pembatalan item parsial
            $table->enum('status_item', ['aktif', 'dibatalkan'])->default('aktif')->comment('utk pembatalan parsial saat stok habis');

            // Waktu data item pesanan dibuat
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
