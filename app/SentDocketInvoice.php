<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketInvoice extends Model
{
    public function sentDocketValueInfo(){
        return $this->hasOne('App\SentDocketsValue', 'id','sent_docket_value_id');
    } 
}
