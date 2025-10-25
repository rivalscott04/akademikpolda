<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            // Ensure there are standalone indexes for FK columns before dropping the composite unique
            try { $table->index('user_id', 'uts_user_id_idx'); } catch (\Throwable $e) {}
            try { $table->index('tryout_id', 'uts_tryout_id_idx'); } catch (\Throwable $e) {}
            try { $table->index('soal_id', 'uts_soal_id_idx'); } catch (\Throwable $e) {}

            // Drop old unique constraint if exists
            // Laravel doesn't know the name automatically for composite unique; assume default naming
            // Try both conventional and explicit names to be safe
            try {
                $table->dropUnique(['user_id', 'tryout_id', 'soal_id']);
            } catch (\Throwable $e) {
                try {
                    $table->dropUnique('user_tryout_soal_user_id_tryout_id_soal_id_unique');
                } catch (\Throwable $e2) {
                    // ignore if already dropped
                }
            }
        });

        Schema::table('user_tryout_soal', function (Blueprint $table) {
            // New uniqueness per session and soal
            if (!Schema::hasColumn('user_tryout_soal', 'user_tryout_session_id')) {
                // Safety: ensure column exists (should already be added by previous migration)
                $table->unsignedBigInteger('user_tryout_session_id')->nullable()->after('tryout_id');
            }
            $table->unique(['user_tryout_session_id', 'soal_id'], 'uts_session_soal_unique');

            // Helpful composite index for lookups
            $table->index(['user_id', 'tryout_id', 'user_tryout_session_id'], 'uts_user_tryout_session_idx');
        });
    }

    public function down(): void
    {
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            // Drop new indexes
            try {
                $table->dropUnique('uts_session_soal_unique');
            } catch (\Throwable $e) {}
            try {
                $table->dropIndex('uts_user_tryout_session_idx');
            } catch (\Throwable $e) {}

            // Restore old unique
            $table->unique(['user_id', 'tryout_id', 'soal_id']);

            // Optionally drop helper indexes added in up()
            try { $table->dropIndex('uts_user_id_idx'); } catch (\Throwable $e) {}
            try { $table->dropIndex('uts_tryout_id_idx'); } catch (\Throwable $e) {}
            try { $table->dropIndex('uts_soal_id_idx'); } catch (\Throwable $e) {}
        });
    }
};


