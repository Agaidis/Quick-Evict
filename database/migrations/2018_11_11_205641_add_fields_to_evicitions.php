<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToEvicitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evictions', function (Blueprint $table) {
            $table->string('property_address');
            $table->string('owner_name');
            $table->string('tenant_name');
            $table->float('court_filing_fee');
            $table->string('pdf_download')->nullable();
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
