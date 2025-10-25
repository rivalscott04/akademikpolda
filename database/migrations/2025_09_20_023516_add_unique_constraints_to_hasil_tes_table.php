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
        Schema::table('hasil_tes', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate test results for same user, test type, and date
            // This prevents multiple entries for the same test session
            $table->unique(['user_id', 'jenis_tes', 'tanggal_tes'], 'unique_user_test_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->dropUnique('unique_user_test_date');
        });
    }
};