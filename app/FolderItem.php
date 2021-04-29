<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FolderItem extends Model
{
    protected $visible = ['ref_id','type'];

    public function folder(){
        return $this->hasOne('App\Folder','id','folder_id');
    }

}
