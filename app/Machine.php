<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = "machines";
    protected $fillable = [
        'name',
        'registration',
        'image',
        'company_id'
    ];
}
