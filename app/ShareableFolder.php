<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShareableFolder extends Model
{
    protected $fillable = ['id','folder_id','shareable_type','link'];

    public function shareableFolderUsers(){
        return $this->hasMany('App\ShareableFolderUser','shareable_folder_id','id');
    }

    public function folder(){
        return $this->hasOne('App\Folder','id','folder_id');
    }
}
