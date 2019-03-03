<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddThreeDefendantAndAdditionalTenant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('court_details', function (Blueprint $table) {
            $table->string('three_defendant_up_to_2000')->nullable();
            $table->string('three_defendant_between_2001_4000')->nullable();
            $table->string('three_defendant_greater_than_4000')->nullable();
            $table->string('three_defendant_out_of_pocket')->nullable();
            $table->integer('additional_tenant')->nullable();
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
            $table->dropcolumn('three_defendant_up_to_2000');
            $table->dropcolumn('three_defendant_between_2001_4000');
            $table->dropcolumn('three_defendant_greater_than_4000');
            $table->dropcolumn('three_defendant_out_of_pocket');
            $table->dropColumn('additional_tenant');
        });
    }
}
