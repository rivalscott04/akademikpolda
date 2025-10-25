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
        // First, expand enum to include both old and new values
        DB::statement("ALTER TABLE hasil_tes MODIFY COLUMN jenis_tes ENUM('kecermatan', 'kecerdasan', 'kepribadian', 'bahasa_inggris', 'pu', 'twk', 'numerik') NULL");
        
        // Then update existing data to match new enum values
        DB::table('hasil_tes')->where('jenis_tes', 'kecerdasan')->update(['jenis_tes' => 'bahasa_inggris']);
        DB::table('hasil_tes')->where('jenis_tes', 'kepribadian')->update(['jenis_tes' => 'pu']);
        DB::table('hasil_tes')->where('jenis_tes', 'kecermatan')->update(['jenis_tes' => 'twk']);
        
        // Finally, remove old enum values
        DB::statement("ALTER TABLE hasil_tes MODIFY COLUMN jenis_tes ENUM('bahasa_inggris', 'pu', 'twk', 'numerik') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old enum values
        DB::statement("ALTER TABLE hasil_tes MODIFY COLUMN jenis_tes ENUM('kecermatan', 'kecerdasan', 'kepribadian') NULL");
    }
};
