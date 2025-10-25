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
        // Update enum to include 'free'
        DB::statement("ALTER TABLE package_category_mappings MODIFY COLUMN package_type ENUM('free', 'kecerdasan', 'kepribadian', 'lengkap') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'free' from enum
        DB::statement("ALTER TABLE package_category_mappings MODIFY COLUMN package_type ENUM('kecerdasan', 'kepribadian', 'lengkap') NOT NULL");
    }
};