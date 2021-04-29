<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimesheetDocketDetail extends Model
{
    public  function  TimesheetDocketAttachment(){
        return $this->hasMany('App\TimesheetDocketAttachment','timesheet_docket_detail_id','id');

    }

    public function UserId(){
        return $this->hasOne('App\User','id','employee_id');
    }
}
