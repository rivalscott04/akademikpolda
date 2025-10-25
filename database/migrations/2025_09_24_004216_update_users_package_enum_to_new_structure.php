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
        // Update existing 'psikologi' values to 'kepribadian' before changing enum
        DB::statement("UPDATE users SET package = 'kepribadian' WHERE package = 'psikologi'");
        
        // Update the package enum to match new structure
        DB::statement("ALTER TABLE users MODIFY COLUMN package ENUM('free', 'kecermatan', 'kecerdasan', 'kepribadian', 'lengkap') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Update 'kepribadian' values back to 'psikologi' before reverting enum
        DB::statement("UPDATE users SET package = 'psikologi' WHERE package = 'kepribadian'");
        
        // Revert back to previous enum structure
        DB::statement("ALTER TABLE users MODIFY COLUMN package ENUM('free', 'kecermatan', 'psikologi', 'lengkap') NULL");
    }
};