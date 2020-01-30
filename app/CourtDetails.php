<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourtDetails extends Model
{
    protected $fillable = [
        'county',
        'court_number',
        'magistrate_id',
        'one_defendant_up_to_2000',
        'two_defendant_up_to_2000',
        'three_defendant_up_to_2000',
        'one_defendant_between_2001_4000',
        'two_defendant_between_2001_4000',
        'three_defendant_between_2001_4000',
        'one_defendant_greater_than_4000',
        'two_defendant_greater_than_4000',
        'three_defendant_greater_than_4000',
        'one_defendant_out_of_pocket',
        'two_defendant_out_of_pocket',
        'three_defendant_out_of_pocket',
        'additional_tenant',
        'out_of_pocket',
        'mailing_address',
        'phone_number',
        'township',
        'digital_signature',
        'online_submission',
        'oop_additional_tenant_fee',
        'civil_mail_additional_tenant_fee',
        'civil_constable_additional_tenant_fee'
    ];

    protected $table = 'court_details';
}
