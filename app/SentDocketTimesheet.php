<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketTimesheet extends Model
{
    public function sendDocket(){
        return $this->hasOne('App\SentDockets','id' , 'sent_docket_id');
    }


}
