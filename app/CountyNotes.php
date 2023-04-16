<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountyNotes extends Model
{
    protected $fillable = [
        'county', 'note'
    ];

    protected $table = 'county_notes';
}
