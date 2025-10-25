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
            $table->enum('pembahasan_type', ['text', 'image', 'both'])->default('text')->after('pembahasan');
            $table->string('pembahasan_image')->nullable()->after('pembahasan_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn(['pembahasan_type', 'pembahasan_image']);
        });
    }
};


