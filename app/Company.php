<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Company extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }

    public function employees(){
        return $this->hasMany('App\Employee','company_id','id');
    }

    public function subscription(){
        return $this->hasOne('App\CompanySubscription','company_id','id');
    }

    public function  trialSubscription(){
        return $this->hasOne('App\SubscriptionPlan','id','subscription_plan_id');
    }

    public function dockets(){
        return $this->hasMany('App\Docket', 'company_id','id');
    }
    public function invoices(){
        return $this->hasMany('App\Invoice', 'company_id','id');
    }

    public function docketLabels(){
        return $this->hasMany('App\DocketLabel','company_id','id')->orderBy('created_at','desc');
    }

    public function invoiceLabels(){
        return $this->hasMany('App\Invoice_Label', 'company_id','id')->orderBy('created_at','desc');
    }

    //client section
    public function clientRequest(){
        return $this->hasMany('App\ClientRequest','requested_company_id','id');
    }
    public function unapprovedClientRequest(){
        return $this->hasMany('App\ClientRequest','company_id','id');
    }
    public function emailClients(){
        return $this->hasMany('App\Email_Client','company_id','id');
    }

    public function  sentDocket(){
        return $this->hasMany('App\SentDockets', 'user_id', 'user_id');
    }

    //message-reminder
    public function messageGroup(){
        return $this->hasMany('App\MessageGroup','company_id','id');
    }

    //helper function
    public function  allCompanyUsers(){
        $employee   =    $this->employees()->pluck('user_id')->toArray();
        return User::whereIn('id',array_merge([$this->user_id],$employee))->get();
    }

    public function timerSetting(){
        $timerSetting = TimerSetting::where('company_id', $this->id)->get();
        if($timerSetting->count()!=0){
            $timerSetting   =    $timerSetting->first();
        }
        else{
            $timerSetting = new TimerSetting();
            $timerSetting->company_id = $this->id;
            $timerSetting->comment_image = 1;
            $timerSetting->pause_image = 1;
            $timerSetting->save();
        }
        return $timerSetting;
    }

    public function getAllCompanyUserIds(){
        $employee =  $this->employees()->pluck('user_id')->toArray();
        return array_merge([$this->user_id],$employee);
    }

    public function folders(){
        return $this->hasMany('App\Folder', 'company_id','id');
    }
}
