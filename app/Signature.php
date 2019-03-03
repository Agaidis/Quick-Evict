<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $fillable = [
        'eviction_id',
        'signature'
    ];

    protected $table = 'signatures';
}
