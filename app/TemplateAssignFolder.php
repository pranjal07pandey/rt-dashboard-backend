<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateAssignFolder extends Model
{
    public function folder(){
        return $this->hasOne('App\Folder','id','folder_id');
    }


}
