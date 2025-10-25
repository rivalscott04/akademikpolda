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
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan');
            $table->enum('tipe', ['benar_salah', 'pg_satu', 'pg_bobot', 'pg_pilih_2']);
            $table->foreignId('kategori_id')->constrained('kategori_soal');
            $table->text('pembahasan')->nullable();
            $table->string('jawaban_benar')->nullable(); // Untuk tipe benar_salah dan pg_satu
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
}; 