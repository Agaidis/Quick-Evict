<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CivilUnique extends Model
{
    protected $fillable = [
        'under_500_1_def_mail',
        'btn_500_2000_1_def_mail',
        'btn_2000_4000_1_def_mail',
        'btn_4000_12000_1_def_mail',
        'under_500_2_def_mail',
        'btn_500_2000_2_def_mail',
        'btn_2000_4000_2_def_mail',
        'btn_4000_12000_2_def_mail',
        'under_500_1_def_constable',
        'btn_500_2000_1_def_constable',
        'btn_2000_4000_1_def_constable',
        'btn_4000_12000_1_def_constable',
        'under_500_2_def_constable',
        'btn_500_2000_2_def_constable',
        'btn_2000_4000_2_def_constable',
        'btn_4000_12000_2_def_constable'
        ];

protected $table = 'civil_unique';
}
