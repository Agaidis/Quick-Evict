<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileAddress extends Model
{
    protected $fillable = ['file_id', 'file_address'];

    protected $table = 'file_addresses';
}
