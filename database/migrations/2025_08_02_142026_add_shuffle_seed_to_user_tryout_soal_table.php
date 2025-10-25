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
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->string('session_seed')->nullable()->after('tryout_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_tryout_soal', function (Blueprint $table) {
            $table->dropColumn('session_seed');
        });
    }
};
