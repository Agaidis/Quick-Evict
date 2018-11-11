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
        'pdf_download'
    ];

    protected $table = 'evictions';
}
