<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignedInvoice extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function assignedBy(){
        return $this->hasOne('App\User', 'id', 'assigned_by');
    }

    public function invoiceInfo(){
        return $this->hasOne('App\Invoice', 'id', 'invoice_id');
    }
}
