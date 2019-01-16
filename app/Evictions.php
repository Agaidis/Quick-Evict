<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evictions extends Model
{
    protected $fillable = [
        'status',
        'total_judgement',
        'property_address',
        'owner_name',
        'tenant_name',
        'court_filing_fee',
        'pdf_download',
        'magistrate_id',
        'court_number',
        'court_address_line_1',
        'court_address_line_2',
        'attorney_fees',
        'damage_amt',
        'due_rent',
        'security_deposit',
        'monthly_rent',
        'unjust_damages',
        'breached_details',
        'property_damage_details',
        'plaintiff_line',
        'is_residential',
        'no_quit_notice',
        'unsatisfied_lease',
        'breached_conditions_lease',
        'amt_greater_than_zero',
        'lease_ended',
        'defendant_state',
        'defendant_zipcode',
        'defendant_house_num',
        'defendant_street_name',
        'defendant_town',
        'filing_fee',
        'pm_name',
        'pm_phone',
        'is_abandoned',
        'is_determination_request',
        'is_additional_rent',
        'additional_rent_amt',
        'unit_num',
        'plantiff_name',
        'plantiff_phone',
        'plantiff_address_line_1',
        'plantiff_address_line_2',
        'verify_name'
    ];

    protected $table = 'evictions';
}
