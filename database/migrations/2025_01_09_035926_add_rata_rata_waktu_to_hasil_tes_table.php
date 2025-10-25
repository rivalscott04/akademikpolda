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
            $table->decimal('average_time', 8, 2)->nullable()->after('waktu_total');
        });
    }

    public function down()
    {
        Schema::table('hasil_tes', function (Blueprint $table) {
            $table->dropColumn('average_time');
        });
    }
};
