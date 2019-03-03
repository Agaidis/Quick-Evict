<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoLocation extends Model
{
    protected $fillable = [
        'magistrate_id',
        'geo_locations',
        'county',
        'court_number',
        'address_line_one',
        'address_line_two'
    ];

    protected $table = 'geo_locations';
}
