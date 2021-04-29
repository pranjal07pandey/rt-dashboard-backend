<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceField extends Model
{
    public function fieldCategoryInfo(){
        return $this->hasOne('App\InvoiceFieldCategory','id','invoice_field_category_id');
    }

}
