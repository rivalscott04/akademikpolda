<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_tryout_sessions')) {
            Schema::table('user_tryout_sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('user_tryout_sessions', 'tkp_final_score')) {
                    $table->decimal('tkp_final_score', 6, 2)->nullable()->after('status');
                }
            });
        }

        if (Schema::hasTable('hasil_tes')) {
            Schema::table('hasil_tes', function (Blueprint $table) {
                if (!Schema::hasColumn('hasil_tes', 'tkp_final_score')) {
                    $table->decimal('tkp_final_score', 6, 2)->nullable()->after('average_time');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_tryout_sessions')) {
            Schema::table('user_tryout_sessions', function (Blueprint $table) {
                if (Schema::hasColumn('user_tryout_sessions', 'tkp_final_score')) {
                    $table->dropColumn('tkp_final_score');
                }
            });
        }

        if (Schema::hasTable('hasil_tes')) {
            Schema::table('hasil_tes', function (Blueprint $table) {
                if (Schema::hasColumn('hasil_tes', 'tkp_final_score')) {
                    $table->dropColumn('tkp_final_score');
                }
            });
        }
    }
};




