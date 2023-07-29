<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EvictionNote extends Model
{
    protected $fillable = [
        'eviction_id', 'note'
    ];

    protected $table = 'eviction_notes';
}
