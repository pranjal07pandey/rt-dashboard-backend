<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimesheetDocketAttachment extends Model
{
    public function TimesheetDocketDetail(){
        return $this->hasOne('App\TimesheetDocketDetail','id','timesheet_docket_detail_id');
    }



}
