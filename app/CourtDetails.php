<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourtDetails extends Model
{
    protected $fillable = [
        'county',
        'court_number',
        '1_defendant_up_to_2000',
        '2_defendant_up_to_2000',
        '1_defendant_between_2001_4000',
        '2_defendant_between_2001_4000',
        '1_defendant_greater_than_4000',
        '2_defendant_greater_than_4000',
        'out_of_pocket',
        'mailing_address',
        'phone_number',
        'accept_e_signature'
    ];

    protected $table = 'court_details';
}
