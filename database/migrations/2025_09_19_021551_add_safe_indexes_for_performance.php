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
        // Helper function untuk cek apakah index sudah ada
        $checkIndexExists = function($table, $indexName) {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        };

        // Users table indexes - cek dulu apakah sudah ada
        if (!$checkIndexExists('users', 'users_email_role_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['email', 'role'], 'users_email_role_index');
            });
        }

        if (!$checkIndexExists('users', 'users_is_active_created_at_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index(['is_active', 'created_at'], 'users_is_active_created_at_index');
            });
        }

        if (!$checkIndexExists('users', 'users_created_at_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('created_at', 'users_created_at_index');
            });
        }

        // Subscriptions table indexes - cek dulu apakah sudah ada
        if (!$checkIndexExists('subscriptions', 'subscriptions_user_id_payment_status_end_date_index')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->index(['user_id', 'payment_status', 'end_date'], 'subscriptions_user_id_payment_status_end_date_index');
            });
        }

        if (!$checkIndexExists('subscriptions', 'subscriptions_payment_status_created_at_index')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->index(['payment_status', 'created_at'], 'subscriptions_payment_status_created_at_index');
            });
        }

        // Soals table indexes
        if (!$checkIndexExists('soals', 'soals_is_active_kategori_id_index')) {
            Schema::table('soals', function (Blueprint $table) {
                $table->index(['is_active', 'kategori_id'], 'soals_is_active_kategori_id_index');
            });
        }

        if (!$checkIndexExists('soals', 'soals_kategori_id_is_active_index')) {
            Schema::table('soals', function (Blueprint $table) {
                $table->index(['kategori_id', 'is_active'], 'soals_kategori_id_is_active_index');
            });
        }

        // Tryouts table indexes
        if (!$checkIndexExists('tryouts', 'tryouts_is_active_created_at_index')) {
            Schema::table('tryouts', function (Blueprint $table) {
                $table->index(['is_active', 'created_at'], 'tryouts_is_active_created_at_index');
            });
        }

        // User tryout sessions table indexes
        if (!$checkIndexExists('user_tryout_sessions', 'user_tryout_sessions_status_finished_at_index')) {
            Schema::table('user_tryout_sessions', function (Blueprint $table) {
                $table->index(['status', 'finished_at'], 'user_tryout_sessions_status_finished_at_index');
            });
        }

        if (!$checkIndexExists('user_tryout_sessions', 'user_tryout_sessions_user_id_status_index')) {
            Schema::table('user_tryout_sessions', function (Blueprint $table) {
                $table->index(['user_id', 'status'], 'user_tryout_sessions_user_id_status_index');
            });
        }

        // User tryout soal table indexes
        if (!$checkIndexExists('user_tryout_soal', 'user_tryout_soal_soal_id_skor_index')) {
            Schema::table('user_tryout_soal', function (Blueprint $table) {
                $table->index(['soal_id', 'skor'], 'user_tryout_soal_soal_id_skor_index');
            });
        }

        if (!$checkIndexExists('user_tryout_soal', 'user_tryout_soal_user_id_tryout_id_index')) {
            Schema::table('user_tryout_soal', function (Blueprint $table) {
                $table->index(['user_id', 'tryout_id'], 'user_tryout_soal_user_id_tryout_id_index');
            });
        }

        // Hasil tes table indexes
        if (!$checkIndexExists('hasil_tes', 'hasil_tes_user_id_skor_akhir_index')) {
            Schema::table('hasil_tes', function (Blueprint $table) {
                $table->index(['user_id', 'skor_akhir'], 'hasil_tes_user_id_skor_akhir_index');
            });
        }

        if (!$checkIndexExists('hasil_tes', 'hasil_tes_skor_akhir_index')) {
            Schema::table('hasil_tes', function (Blueprint $table) {
                $table->index('skor_akhir', 'hasil_tes_skor_akhir_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Helper function untuk cek apakah index ada sebelum drop
        $checkIndexExists = function($table, $indexName) {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($indexes) > 0;
        };

        // Drop indexes dengan pengecekan
        if ($checkIndexExists('users', 'users_email_role_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_email_role_index');
            });
        }

        if ($checkIndexExists('users', 'users_is_active_created_at_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_is_active_created_at_index');
            });
        }

        if ($checkIndexExists('users', 'users_created_at_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_created_at_index');
            });
        }

        if ($checkIndexExists('subscriptions', 'subscriptions_user_id_payment_status_end_date_index')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropIndex('subscriptions_user_id_payment_status_end_date_index');
            });
        }

        if ($checkIndexExists('subscriptions', 'subscriptions_payment_status_created_at_index')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropIndex('subscriptions_payment_status_created_at_index');
            });
        }

        if ($checkIndexExists('soals', 'soals_is_active_kategori_id_index')) {
            Schema::table('soals', function (Blueprint $table) {
                $table->dropIndex('soals_is_active_kategori_id_index');
            });
        }

        if ($checkIndexExists('soals', 'soals_kategori_id_is_active_index')) {
            Schema::table('soals', function (Blueprint $table) {
                $table->dropIndex('soals_kategori_id_is_active_index');
            });
        }

        if ($checkIndexExists('tryouts', 'tryouts_is_active_created_at_index')) {
            Schema::table('tryouts', function (Blueprint $table) {
                $table->dropIndex('tryouts_is_active_created_at_index');
            });
        }

        if ($checkIndexExists('user_tryout_sessions', 'user_tryout_sessions_status_finished_at_index')) {
            Schema::table('user_tryout_sessions', function (Blueprint $table) {
                $table->dropIndex('user_tryout_sessions_status_finished_at_index');
            });
        }

        if ($checkIndexExists('user_tryout_sessions', 'user_tryout_sessions_user_id_status_index')) {
            Schema::table('user_tryout_sessions', function (Blueprint $table) {
                $table->dropIndex('user_tryout_sessions_user_id_status_index');
            });
        }

        if ($checkIndexExists('user_tryout_soal', 'user_tryout_soal_soal_id_skor_index')) {
            Schema::table('user_tryout_soal', function (Blueprint $table) {
                $table->dropIndex('user_tryout_soal_soal_id_skor_index');
            });
        }

        if ($checkIndexExists('user_tryout_soal', 'user_tryout_soal_user_id_tryout_id_index')) {
            Schema::table('user_tryout_soal', function (Blueprint $table) {
                $table->dropIndex('user_tryout_soal_user_id_tryout_id_index');
            });
        }

        if ($checkIndexExists('hasil_tes', 'hasil_tes_user_id_skor_akhir_index')) {
            Schema::table('hasil_tes', function (Blueprint $table) {
                $table->dropIndex('hasil_tes_user_id_skor_akhir_index');
            });
        }

        if ($checkIndexExists('hasil_tes', 'hasil_tes_skor_akhir_index')) {
            Schema::table('hasil_tes', function (Blueprint $table) {
                $table->dropIndex('hasil_tes_skor_akhir_index');
            });
        }
    }
};