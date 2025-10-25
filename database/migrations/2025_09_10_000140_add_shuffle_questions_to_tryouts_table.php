<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tryouts', function (Blueprint $table) {
            if (!Schema::hasColumn('tryouts', 'shuffle_questions')) {
                $table->boolean('shuffle_questions')->default(false)->after('struktur');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tryouts', function (Blueprint $table) {
            if (Schema::hasColumn('tryouts', 'shuffle_questions')) {
                $table->dropColumn('shuffle_questions');
            }
        });
    }
};


