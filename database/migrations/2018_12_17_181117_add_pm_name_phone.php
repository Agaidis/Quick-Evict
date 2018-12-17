<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPmNamePhone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evictions', function (Blueprint $table) {
            $table->string('pm_name')->nullable();
            $table->string('pm_phone')->nullable();
            $table->boolean('is_abandoned')->nullable();
            $table->boolean('is_determination_request')->nullable();
            $table->string('unit_num')->nullable();
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
