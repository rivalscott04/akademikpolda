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
        Schema::create('package_limits', function (Blueprint $table) {
            $table->id();
            $table->enum('package_type', ['free', 'bahasa_inggris', 'pu', 'twk', 'numerik', 'lengkap']);
            $table->integer('max_tryouts')->default(1); // Jumlah tryout yang bisa diakses
            $table->json('allowed_categories'); // Kategori soal yang boleh diakses
            $table->timestamps();
            
            $table->unique('package_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_limits');
    }
};
