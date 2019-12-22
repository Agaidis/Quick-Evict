<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCivilUniqueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('civil_unique', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('court_details_id');

            $table->string('under_500_1_def_mail')->nullable();
            $table->string('btn_500_2000_1_def_mail')->nullable();
            $table->string('btn_2000_4000_1_def_mail')->nullable();
            $table->string('btn_4000_12000_1_def_mail')->nullable();
            $table->string('under_500_2_def_mail')->nullable();
            $table->string('btn_500_2000_2_def_mail')->nullable();
            $table->string('btn_2000_4000_2_def_mail')->nullable();
            $table->string('btn_4000_12000_2_def_mail')->nullable();

            $table->string('under_500_1_def_constable')->nullable();
            $table->string('btn_500_2000_1_def_constable')->nullable();
            $table->string('btn_2000_4000_1_def_constable')->nullable();
            $table->string('btn_4000_12000_1_def_constable')->nullable();
            $table->string('under_500_2_def_constable')->nullable();
            $table->string('btn_500_2000_2_def_constable')->nullable();
            $table->string('btn_2000_4000_2_def_constable')->nullable();
            $table->string('btn_4000_12000_2_def_constable')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('civil_unique');
    }
}
