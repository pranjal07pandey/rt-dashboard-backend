<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignDocketUserConnection extends Model
{
    protected $table="assign_docket_user_connection";
    protected $fillable = [
        'assign_docket_id',
        'user_id',
        'machine_id',
        'docket_id',
        'status'
    ];

    public function assignDocketUser(){
        return $this->belongsTo(AssignDocketUser::class,'assign_docket_id');
    }

    public function machine(){
        return $this->belongsTo(Machine::class,'machine_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
