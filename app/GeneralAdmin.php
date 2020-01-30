<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralAdmin extends Model
{
    protected $fillable = [
        'name',
        'value'
    ];

    protected $table = 'general_admin';
}
