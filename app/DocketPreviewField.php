<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketPreviewField extends Model
{
    public function docket_filed_info(){
        return $this->hasOne('App\DocketField','id','docket_field_id')->withTrashed();
    }
}
