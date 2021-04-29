<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use App\Mail\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Dlimars\LaravelSearchable\Searchable;
use Illuminate\Support\Facades\Crypt;
use Mail;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;
    protected $webhook;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','email_verification'
    ];
    protected $subject = "New subject... :)";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function encryptedID(){
        return Crypt::encrypt($this->id);
    }

    public function employeeInfo(){
        return $this->hasOne('App\Employee','user_id','id');
    }
    public function companyInfo(){
        return $this->hasOne('App\Company','user_id','id');
    }
    public function lastSentDocket(){
        return $this->hasOne('App\SentDockets','user_id','id');
    }
    public function totalDocketSent(){
        return $this->hasMany('App\SentDockets','user_id','id');
    }
    public function totalDocketReceived(){
        return $this->hasMany('App\SentDocketRecipient','user_id','id');
    }
    public function totalInvoiceSent(){
        return $this->hasMany('App\SentInvoice','user_id','id');
    }
    public function totalInvoiceReceived(){
        return $this->hasMany('App\SentInvoice','receiver_user_id','id');
    }

    public function totalEmailedDocketSent(){
        return $this->hasMany('App\EmailSentDocket','user_id','id');
    }

    public function totalEmailedInvoiceSent(){
        return $this->hasMany('App\EmailSentInvoice','user_id','id');
    }

    public function company(){
        if(Employee::where('user_id', $this->id)->count()!=0):
            $employee = Employee::where('user_id', $this->id)->first();
            $company = $employee->companyInfo;
        else :
            $company   =   Company::where('user_id', $this->id)->first();
        endif;
        return $company;
    }

    public function sendPasswordResetNotification($token){
        Mail::to($this->email)->send(new ResetPassword($this, $token));
    }

    //message-reminders
    public function messagesGroupUser(){
        return $this->hasMany('App\MessagesGroupUser','user_id','id');
    }

    private $searchable = [
        'name'          => 'LIKE',
        'id'            => 'MATCH',
        'created_at'    => 'BETWEEN'
    ];

    public function routeNotificationForSlack($notification){
        if($this->webhook) {
            return config('slack.channels.' . $this->webhook);
        }
    }
    public function slackChannel($channel){
        $this->webhook = $channel;
        return $this;
    }

    public function getFullNameAttribute()
    {
       return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }
}
