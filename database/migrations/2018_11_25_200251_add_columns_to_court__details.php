<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToCourtDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evictions', function (Blueprint $table) {
            $table->string('magistrate_id')->nullable();
            $table->string('court_number')->nullable();
            $table->string('court_address_line_1')->nullable();
            $table->string('court_address_line_2')->nullable();
            $table->string('attorney_fees')->nullable();
            $table->string('damage_amt')->nullable();
            $table->string('due_rent')->nullable();
            $table->string('security_deposit')->nullable();
            $table->string('monthly_rent')->nullable();
            $table->string('unjust_damages')->nullable();
            $table->string('breached_details')->nullable();
            $table->string('property_damage_details')->nullable();
            $table->string('plaintiff_line')->nullable();
            $table->boolean('is_residential')->nullable();
            $table->boolean('no_quit_notice')->nullable();
            $table->boolean('unsatisfied_lease')->nullable();
            $table->boolean('breached_conditions_lease')->nullable();
            $table->boolean('amt_greater_than_zero')->nullable();
            $table->boolean('lease_ended')->nullable();
            $table->string('defendant_state')->nullable();
            $table->string('defendant_zipcode')->nullable();
            $table->string('defendant_house_num')->nullable();
            $table->string('defendant_street_name')->nullable();
            $table->string('defendant_town')->nullable();
            $table->string('filing_fee')->nullable();
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
