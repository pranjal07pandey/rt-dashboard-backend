<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketProject extends Model
{

    public function project(){
        return $this->hasOne('App\Project', 'id','project_id');
    }

}
