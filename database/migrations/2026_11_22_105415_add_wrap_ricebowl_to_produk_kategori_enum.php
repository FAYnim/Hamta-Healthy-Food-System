<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE produk MODIFY COLUMN kategori ENUM('Salad', 'Sandwich', 'Wrap', 'Juice', 'RiceBowl') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE produk MODIFY COLUMN kategori ENUM('Salad', 'Sandwich', 'Juice') NOT NULL");
    }
};
