<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignDocketUser extends Model
{
    protected $table="assign_docket_user";
    protected $fillable = [
        'name',
        'assigned_by',
        'from_date',
        'to_date',
        'bgcolor',
        'comment'
    ];

    public function assignDocketUserConnection(){
        return $this->hasMany(AssignDocketUserConnection::class,'assign_docket_id','id');
    }
}
