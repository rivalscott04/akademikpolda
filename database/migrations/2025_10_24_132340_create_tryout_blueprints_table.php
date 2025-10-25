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
        Schema::create('tryout_blueprints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tryout_id');
            $table->unsignedBigInteger('kategori_id');
            $table->enum('level', ['dasar', 'mudah', 'sedang', 'sulit', 'tersulit', 'ekstrem']);
            $table->integer('jumlah');
            $table->timestamps();

            $table->foreign('tryout_id')->references('id')->on('tryouts')->onDelete('cascade');
            $table->foreign('kategori_id')->references('id')->on('kategori_soal')->onDelete('cascade');
            
            $table->index(['tryout_id', 'kategori_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_blueprints');
    }
};