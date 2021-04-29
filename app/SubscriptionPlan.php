<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    public function description(){
        return $this->hasMany('App\SubscriptionPlanDescription','subscription_plan_id','id');
    }
}
