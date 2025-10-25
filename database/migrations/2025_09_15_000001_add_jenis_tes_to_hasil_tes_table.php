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
        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->enum('jenis_tes', ['kecermatan', 'kecerdasan', 'kepribadian'])->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->dropColumn('jenis_tes');
        });
    }
};


