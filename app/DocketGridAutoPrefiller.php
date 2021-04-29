<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketGridAutoPrefiller extends Model
{
    protected $fillable = [
        'index','link_grid_field_id'
    ];
}
