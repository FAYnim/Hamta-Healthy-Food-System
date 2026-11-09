<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('midtrans_order_id', 100)->nullable()->unique()->after('metode_bayar');
            $table->json('midtrans_response')->nullable()->after('midtrans_order_id');
        });

        // Update ENUM metode_bayar & status_pembayaran pada MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pesanan MODIFY COLUMN metode_bayar ENUM('qris', 'qris_statis', 'tunai', 'midtrans') NOT NULL DEFAULT 'qris'");
            DB::statement("ALTER TABLE pesanan MODIFY COLUMN status_pembayaran ENUM('menunggu_verifikasi', 'terverifikasi', 'ditolak', 'pending', 'settlement', 'expired', 'gagal') NOT NULL DEFAULT 'menunggu_verifikasi'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE pesanan MODIFY COLUMN status_pembayaran ENUM('menunggu_verifikasi', 'terverifikasi', 'ditolak') NOT NULL DEFAULT 'menunggu_verifikasi'");
            DB::statement("ALTER TABLE pesanan MODIFY COLUMN metode_bayar ENUM('qris', 'tunai') NOT NULL DEFAULT 'qris'");
        }

        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['midtrans_response', 'midtrans_order_id']);
        });
    }
};
