<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixTotalJudgement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evictions', function (Blueprint $table) {
            $table->dropColumn('total_judgement');
        });

        Schema::table('evictions', function (Blueprint $table) {
            $table->float('total_judgement', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evictions', function (Blueprint $table) {
            //
        });
    }
}
