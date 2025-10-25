<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            if (!Schema::hasColumn('user_tryout_soal', 'user_tryout_session_id')) {
                $table->unsignedBigInteger('user_tryout_session_id')->nullable()->after('tryout_id');
                $table->index('user_tryout_session_id', 'uts_session_idx');
            }
        });

        // Add foreign key in a separate statement to avoid issues on some DBs
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            if (Schema::hasColumn('user_tryout_soal', 'user_tryout_session_id')) {
                $table->foreign('user_tryout_session_id', 'uts_session_fk')
                    ->references('id')->on('user_tryout_sessions')
                    ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            if (Schema::hasColumn('user_tryout_soal', 'user_tryout_session_id')) {
                $table->dropForeign('uts_session_fk');
                $table->dropIndex('uts_session_idx');
                $table->dropColumn('user_tryout_session_id');
            }
        });
    }
};


