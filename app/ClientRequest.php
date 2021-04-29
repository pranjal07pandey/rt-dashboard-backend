<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ClientRequest extends Model
{
    public function companyInfo(){
        return $this->hasOne('App\Company','id','company_id');
    }

    public function requestedCompanyInfo(){
        return $this->hasOne('App\Company','id','requested_company_id');
    }

    public function formattedCreatedDate(){
        return Carbon::parse($this->created_at)->format('d-M-Y');
    }
}
