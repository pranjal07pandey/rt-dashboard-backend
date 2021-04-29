<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use App\SentDcoketTimerAttachment;
use Illuminate\Support\Facades\Session;

class EmailSentDocket extends Model
{
    use SoftDeletes;

    public function docketInfo(){
        return $this->hasOne('App\Docket','id','docket_id');
    }

    public function senderUserInfo(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function receiverUserInfo(){
        return $this->hasOne('App\EmailUser', 'id', 'receiver_user_id');
    }

    public function senderCompanyInfo(){
        return $this->hasOne('App\Company','id' , 'company_id');
    }
    public function sentDocketValue(){
        return $this->hasMany('App\EmailSentDocketValue','email_sent_docket_id' , 'id');
    }
    public function sentDocketPreviewValue(){
        return $this->hasOne('App\EmailSentDocketValue','email_sent_docket_id','id');
    }
    public function sentDocketPreviewValueBySentDocketId($id,$docketFieldId){
        return $this->sentDocketPreviewValue()->where('email_sent_docket_id',$id)->where('docket_field_id',$docketFieldId)->first();
    }
    //for labeling
    public function sentEmailDocketLabels(){
        return $this->hasMany('App\SentEmailDocketLabel','email_sent_docket_id','id');
    }

    public function recipientInfo(){
        return $this->hasMany('App\EmailSentDocketRecipient','email_sent_docket_id','id');
    }

    //new modification by dileep

    public function attachedTimer(){
        return SentDcoketTimerAttachment::where('sent_docket_id',$this->id)->where('type',2)->get();
    }

    //helper function
    public function encryptedID(){
        return Crypt::encrypt($this->id);
    }
    public function formattedDocketID(){
        return 'rt-'.$this->company_id.'-edoc-'.$this->company_docket_id;
    }
    public function formattedCreatedDate(){
        return Carbon::parse($this->created_at)->format('d-M-Y');
    }

    public function getDistinctRecipientCompany(){
        return EmailSentDocketRecipient::where('email_sent_docket_id', $this->id)->distinct('receiver_company_name')->pluck('receiver_company_name')->toArray();
    }

    public function folder(){
        $folderQuery    =    FolderItem::with('folder')->where('ref_id', $this->id)->where('type',3)->get();
        if($folderQuery->count()>0)
            return $folderQuery->first();
        else
            return null;
    }

    public function shareFolderUserId(){
        $shareAbleFolder = ShareableFolder::where('link',Session::get('shareable_folder')['link'])->first();
        if($shareAbleFolder->shareable_type =="Restricted"){
            $userToken = Session::get('shareable_folder')['token'];
            $shareAbleFolderuser = ShareableFolderUser::where('token',$userToken)->first();
            if($shareAbleFolderuser != null){
                $users =  EmailUser::where('email',$shareAbleFolderuser->email)->select('id','email')->get();
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



    public function sentDocketRecipientApproval(){
        return $this->hasMany('App\EmailSentDocketRecipient','email_sent_docket_id','id');

    }
    public function sentDocketRecipientApproved(){
        return $this->sentDocketRecipientApproval()->where('status',1);
    }
}
