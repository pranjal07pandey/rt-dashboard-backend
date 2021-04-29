<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use App\Support\Collection;
use App\SentDcoketTimerAttachment;
use Illuminate\Support\Facades\Session;

class SentDockets extends Model
{
    use SoftDeletes;

    public function docketInfo(){
        return $this->hasOne('App\Docket','id','docket_id');
    }
    public function senderUserInfo(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function senderCompanyInfo(){
        return $this->hasOne('App\Company','id' , 'sender_company_id');
    }
    public function recipientInfo(){
        return $this->hasMany('App\SentDocketRecipient','sent_docket_id','id');
    }
    public function receiverUserInfo(){
        return $this->hasOne('App\User', 'id', 'receiver_user_id');
    }
    public function companyInfo(){
        return $this->hasOne('App\Company','id' , 'company_id');
    }
    public function invoiceDetails(){
        return $this->hasOne('App\SentDocketInvoiceDetail','sent_docket_id' , 'id');
    }
    public function sentDocketValue(){
        return $this->hasMany('App\SentDocketsValue','sent_docket_id' , 'id');
    }

    public function sentDocketPreviewValue(){
        return $this->hasOne('App\SentDocketsValue','sent_docket_id','id');
    }
    public function sentDocketPreviewValueBySentDocketId($id,$docketFieldId){
        return $this->sentDocketPreviewValue()->where('sent_docket_id',$id)->where('docket_field_id',$docketFieldId)->first();
    }
    public function sentDocketRecipientApproval(){
        return $this->hasMany('App\SentDocketRecipientApproval','sent_docket_id','id');
    }
    public function sentDocketRecipientApproved(){
        return $this->sentDocketRecipientApproval()->where('status',1);
    }

    public function sentDocketRecipientUnapproved(){
        return $this->sentDocketRecipientApproval()->where('status',0);
    }
    //for labeling
    public function sentDocketLabels(){
        return $this->hasMany('App\SentDocketLabel','sent_docket_id','id');
    }

    public function sentDocketTimesheet(){
        return $this->hasMany('App\SentDocketTimesheet','sent_docket_id' , 'id');
    }

    public function sentDocketTimerAttachment(){
        return $this->hasMany('App\SentDcoketTimerAttachment','sent_docket_id','id');
    }
    public function sentDocketRejectExplanation(){
        return $this->hasMany('App\SentDocketReject','sent_docket_id','id');
    }

    //helper function
    public function encryptedID(){
        return Crypt::encrypt($this->id);
    }

    public function shareFolderUserId(){
      $shareAbleFolder = ShareableFolder::where('link',Session::get('shareable_folder')['link'])->first();
      if($shareAbleFolder->shareable_type =="Restricted"){
          $userToken = Session::get('shareable_folder')['token'];
          $shareAbleFolderuser = ShareableFolderUser::where('token',$userToken)->first();
          if($shareAbleFolderuser != null){
            $users =  User::where('email',$shareAbleFolderuser->email)->select('id','email')->get();
            if(count($users) == 0){
                $user = array('id'=> "",'email'=> "");
            }else{
                $user = array('id'=>@$users[0]['id'],'email'=>@$users[0]['email']);
            }

          }
          $data =   array('user'=>$user,"type"=>"Restricted");
          return $data;
      }else if($shareAbleFolder->shareable_type =="Public"){
          $data =   array('user'=>[],"type"=> "Public");
          return $data;
      }
    }

    public function formattedDocketID(){
        return 'rt-'.$this->sender_company_id.'-doc-'.$this->company_docket_id;
    }

    public function formattedCreatedDate(){
        return Carbon::parse($this->created_at)->format('d-M-Y');
    }

    public function validUsers(){
        $sender         =   array($this->user_id);
        $recipients     =   $this->recipientInfo()->pluck('user_id')->toArray();
        $validUsers     =   User::whereIn('id',array_merge($sender,$recipients))->get();
        return $validUsers;
    }

    public function formattedRecipientList(){
        $sentDocketRecipients = array();
        $companyNameRecipient   =   "";

        foreach($this->recipientInfo as $sentDocketRecepient){
            if (@$sentDocketRecepient->userInfo->employeeInfo){
                $companyNameRecipient = $sentDocketRecepient->userInfo->employeeInfo->companyInfo->name;
            }else if (@$sentDocketRecepient->userInfo->companyInfo){
                $companyNameRecipient = $sentDocketRecepient->userInfo->companyInfo->name;
            }

            $sentDocketRecipients[]=array(
                'name'=>@$sentDocketRecepient->userInfo->first_name." ".@$sentDocketRecepient->userInfo->last_name,
                'company_name'=> $companyNameRecipient,
            );
        }
               $receiverDetail = array();
        $data= (new Collection($sentDocketRecipients))->sortBy('company_name');
        foreach ($data as $datas){
            $receiverDetail[$datas['company_name']][]= $datas['name'];
        }
        return $receiverDetail;
    }

    public function attachedTimer(){
        return SentDcoketTimerAttachment::where('sent_docket_id',$this->id)->where('type',1)->get();
    }

    public function folder(){
        $folderQuery    =    FolderItem::with('folder')->where('ref_id', $this->id)->where('type',1)->get();
        if($folderQuery->count()>0)
            return $folderQuery->first();
        else
            return null;
    }

    /**
     *
     */
    public function invoiceAmount(){
        $invoiceAmount  =    0;
        $invoiceAmountQuery    =    SentDocketInvoice::with('sentDocketValueInfo.sentDocketUnitRateValue')->where('sent_docket_id',$this->id)->where('type',2)->get();
        foreach($invoiceAmountQuery as $amount){
            $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
            if(is_numeric($unitRate[0]["value"])){ $unitRate1= $unitRate[0]["value"]; }
            else{ $unitRate1=0;}

            if(is_numeric($unitRate[1]["value"])){ $unitRate2= $unitRate[1]["value"]; }
            else{ $unitRate2= 0; }
            $invoiceAmount   =  $invoiceAmount + $unitRate1 * $unitRate2 ;
        }
        return $invoiceAmount;
    }
}
