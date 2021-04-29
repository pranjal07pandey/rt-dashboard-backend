<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShareableFolderUser extends Model
{
    protected $fillable = ['id','shareable_folder_id','email','password','token'];

    public function shareableFolder(){
        return $this->hasOne('App\ShareableFolder','id','shareable_folder_id');
    }
}
