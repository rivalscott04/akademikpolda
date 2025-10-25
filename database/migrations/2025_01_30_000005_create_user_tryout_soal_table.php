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
        Schema::create('user_tryout_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tryout_id')->constrained()->onDelete('cascade');
            $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
            $table->integer('urutan')->default(0);
            $table->json('jawaban_user')->nullable(); // Menyimpan jawaban user
            $table->decimal('skor', 5, 2)->nullable(); // Skor untuk soal ini
            $table->integer('waktu_jawab')->nullable(); // Waktu dalam detik
            $table->boolean('sudah_dijawab')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'tryout_id', 'soal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tryout_soal');
    }
}; 