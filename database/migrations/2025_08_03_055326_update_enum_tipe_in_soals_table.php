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
        Schema::table('soals', function (Blueprint $table) {
            DB::statement("ALTER TABLE soals MODIFY tipe ENUM('benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2', 'gambar') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            DB::statement("ALTER TABLE soals MODIFY tipe ENUM('benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2') NOT NULL");
        });
    }
};
