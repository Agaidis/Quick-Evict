<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourtNotification extends Model
{
    protected $fillable = [
        'court_number',
        'court_date',
        'user_id',
    ];

    protected $table = 'court_notifications';
}
