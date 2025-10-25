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
        Schema::create('opsi_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
            $table->char('opsi', 1); // A, B, C, D, E
            $table->text('teks');
            $table->decimal('bobot', 3, 2)->default(0.00); // Nilai bobot 0.00 - 1.00
            $table->timestamps();
            
            $table->unique(['soal_id', 'opsi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opsi_soal');
    }
}; 