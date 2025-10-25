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
            $table->decimal('panker', 8, 2)->nullable()->after('detail_jawaban');
            $table->decimal('tianker', 8, 2)->nullable()->after('panker');
            $table->decimal('janker', 8, 2)->nullable()->after('tianker');
            $table->decimal('hanker', 8, 4)->nullable()->after('janker');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->dropColumn(['panker', 'tianker', 'janker', 'hanker']);
        });
    }
};
