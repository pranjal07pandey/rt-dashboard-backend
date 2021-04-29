<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    public function childs() {
        return $this->hasMany('App\Folder','root_id','id') ;
    }

    public function folderItems(){
        return $this->hasMany('App\FolderItem','folder_id','id');
    }

    public function shareableFolder(){
        return $this->hasOne('App\ShareableFolder','folder_id','id');
    }
}
