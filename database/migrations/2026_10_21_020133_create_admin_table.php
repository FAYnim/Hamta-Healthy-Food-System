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
        Schema::create('admin', function (Blueprint $table) {
            // Primary key admin
            $table->id('id_admin');

            // Nama lengkap admin/pengelola
            $table->string('nama', 100);

            // Username unik untuk login admin
            $table->string('username', 50)->unique();

            // Hash password akun admin
            $table->string('password', 255);

            // Waktu data admin dibuat dan diperbarui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
