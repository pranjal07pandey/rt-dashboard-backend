<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketFrequency extends Model
{
    protected $fillable = ["company_id", "frequency_value"];

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }
}
