<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixCourtDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('court_details', function (Blueprint $table) {
            $table->dropColumn('out_of_pocket');
            $table->string('1_defendant_out_of_pocket')->nullable();
            $table->string('2_defendant_out_of_pocket')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('court_details', function (Blueprint $table) {
            //
        });
    }
}
