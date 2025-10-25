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
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->enum('level', ['dasar', 'mudah', 'sedang', 'sulit', 'tersulit', 'ekstrem'])->default('mudah')->after('soal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};