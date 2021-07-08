<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalTenantFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('court_details', function (Blueprint $table) {
            $table->string('oop_additional_tenant_fee')->nullable();
            $table->string('civil_mail_additional_tenant_fee')->nullable();
            $table->string('civil_constable_additional_tenant_fee')->nullable();
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
