<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCourtDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('court_details', function (Blueprint $table) {
            $table->string('county');
            $table->string('court_number');
            $table->string('1_defendant_up_to_2000')->nullable();
            $table->string('2_defendant_up_to_2000')->nullable();
            $table->string('1_defendant_between_2001_4000')->nullable();
            $table->string('2_defendant_between_2001_4000')->nullable();
            $table->string('1_defendant_greater_than_4000')->nullable();
            $table->string('2_defendant_greater_than_4000')->nullable();
            $table->string('out_of_pocket')->nullable();
            $table->string('mailing_address')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('accept_e_signature')->nullable();
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
            $table->dropColumn('county');
            $table->dropColumn('court_number');
            $table->dropColumn('1_defendant_up_to_2000');
            $table->dropColumn('2_defendant_up_to_2000');
            $table->dropColumn('1_defendant_between_2001_4000');
            $table->dropColumn('2_defendant_between_2001_4000');
            $table->dropColumn('1_defendant_greater_than_4000');
            $table->dropColumn('2_defendant_greater_than_4000');
            $table->dropColumn('out_of_pocket');
            $table->dropColumn('mailing_address');
            $table->dropColumn('phone_number');
            $table->dropColumn('accept_e_signature');
        });
    }
}
