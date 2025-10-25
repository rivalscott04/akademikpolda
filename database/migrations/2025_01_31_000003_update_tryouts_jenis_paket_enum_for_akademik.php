<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update enum to include AKADEMIK packages
        DB::statement("ALTER TABLE tryouts MODIFY COLUMN jenis_paket ENUM('free', 'bahasa_inggris', 'pu', 'twk', 'numerik', 'lengkap') DEFAULT 'free'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old enum values
        DB::statement("ALTER TABLE tryouts MODIFY COLUMN jenis_paket ENUM('free', 'kecerdasan', 'kepribadian', 'lengkap') DEFAULT 'free'");
    }
};
