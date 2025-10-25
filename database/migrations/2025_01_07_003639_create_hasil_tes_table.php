<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('hasil_tes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->integer('skor_benar');
            $table->integer('skor_salah');
            $table->integer('waktu_total');
            $table->json('detail_jawaban');
            $table->datetime('tanggal_tes');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hasil_tes');
    }
};
