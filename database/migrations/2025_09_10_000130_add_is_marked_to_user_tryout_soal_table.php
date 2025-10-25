<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            if (!Schema::hasColumn('user_tryout_soal', 'is_marked')) {
                $table->boolean('is_marked')->default(false)->after('sudah_dijawab');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            if (Schema::hasColumn('user_tryout_soal', 'is_marked')) {
                $table->dropColumn('is_marked');
            }
        });
    }
};


