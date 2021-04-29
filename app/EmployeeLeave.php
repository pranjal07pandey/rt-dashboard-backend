<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    protected $table='leave_management';

    protected $fillable = [
        'user_id',
        'from_date',
        'to_date',
        'description',
        'machine_id'
    ];

    public function userInfo(){
        return $this->hasOne('App\User','id','user_id');
    }

    public function machine(){
        return $this->belongsTo(Machine::class,'machine_id');
    }
}
