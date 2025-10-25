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
            // Drop existing unique constraint
            $table->dropUnique('unique_user_test_date');
            
            // Add improved unique constraint based on user_id, jenis_tes, and created_at (rounded to minutes)
            // This prevents duplicate entries within the same minute for the same user and test type
            $table->unique(['user_id', 'jenis_tes', 'created_at'], 'unique_user_test_created_minute');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->dropUnique('unique_user_test_created_minute');
            $table->unique(['user_id', 'jenis_tes', 'tanggal_tes'], 'unique_user_test_date');
        });
    }
};