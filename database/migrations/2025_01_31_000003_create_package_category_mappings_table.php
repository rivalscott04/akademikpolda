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
        Schema::create('package_category_mappings', function (Blueprint $table) {
            $table->id();
            $table->enum('package_type', ['kecerdasan', 'kepribadian', 'lengkap']);
            $table->unsignedBigInteger('kategori_id');
            $table->timestamps();
            
            $table->foreign('kategori_id')->references('id')->on('kategori_soal')->onDelete('cascade');
            $table->unique(['package_type', 'kategori_id'], 'unique_package_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_category_mappings');
    }
};
