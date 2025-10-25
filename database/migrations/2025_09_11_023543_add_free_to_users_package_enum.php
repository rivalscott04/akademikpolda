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
        // Update the package enum to include 'free' option
        DB::statement("ALTER TABLE users MODIFY COLUMN package ENUM('free', 'kecermatan', 'psikologi', 'lengkap') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum without 'free'
        DB::statement("ALTER TABLE users MODIFY COLUMN package ENUM('kecermatan', 'psikologi', 'lengkap') NULL");
    }
};