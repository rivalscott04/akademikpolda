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
            $table->decimal('skor_akhir', 8, 2)->nullable()->after('hanker');
            $table->string('kategori_skor')->nullable()->after('skor_akhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->dropColumn(['skor_akhir', 'kategori_skor']);
        });
    }
};
