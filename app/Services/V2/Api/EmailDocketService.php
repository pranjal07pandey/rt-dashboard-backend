<?php
namespace App\Services\V2\Api;

use App\Helpers\V2\MessageDisplay;
use Carbon\Carbon;
use App\Services\V2\ConstructorService;
use App\Helpers\V2\AmazoneBucket;
class EmailDocketService extends ConstructorService {

    public function show($request,$id){
        $sentDocket     =  $this->emailSentDocketRepository->getDataWhere([['id',$id]]);
        $webView = $this->getEmailDocketDetailsByIdWebView($request, $id);
        if($sentDocket->count()==1):
            //check docket associated with user or not
            $companyId  =    auth()->user()->companyInfo->id;
            if($sentDocket->where('company_id',$companyId)->count()>0){
                $sentDocketValueQuery    =  $this->emailSentDocketValueRepository->getDataWhere([['email_sent_docket_id',$id]])->get();
                $sentDocketValue    = array();
                foreach ($sentDocketValueQuery as $row){
                    $subFiled   =   [];
                    $value = null;
                    $footersIsTrue = false;
                    if($row->docketFieldInfo->docket_field_category_id==7):
                        foreach($row->sentDocketUnitRateValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'label' => $subFiledRow->docketUnitRateInfo->label,
                                'value' => $subFiledRow->value);
                        endforeach;
                        $value = $row->value;
                    elseif($row->docketFieldInfo->docket_field_category_id==9):
                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;
                        $value = AmazoneBucket::url() . $row->value;
                    elseif($row->docketFieldInfo->docket_field_category_id==14):
                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;
                        $value = AmazoneBucket::url() . $row->value;
                    elseif($row->docketFieldInfo->docket_field_category_id==5):
                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;
                        $value = AmazoneBucket::url() . $row->value;
                    elseif ($row->docketFieldInfo->docket_field_category_id==13):
                        $footerDetails = $this->docketFieldFooterRepository->getDataWhere([["field_id", $row->docket_field_id]])->select('id', 'value')->orderBy('id', 'asc')->first();
                        $value = @$footerDetails->value;
                        $footersIsTrue = true;
                    elseif($row->docketFieldInfo->docket_field_category_id==15):
                        foreach($row->sentEmailAttachment as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'name'=>$subFiledRow->name,
                                'url' => AmazoneBucket::url() . $subFiledRow->url);
                        endforeach;
                        $value = AmazoneBucket::url() . $row->url;
                    else:
                        $value = $row->value;
                    endif;
                }
                $temp = ['id' => $row->id,
                        'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                        'docket_field_category' =>  $row->label,
                        'label' => $row->label,
                        'value' => $value,
                        'subFiled' => $subFiled];
                if($footersIsTrue){
                    $footers = $temp;
                }else{
                    $sentDocketValue[] = $temp;
                }
                if(@$footers){
                    $sentDocketValue[] =   $footers;
                }

                //approval text
                $totalRecipientApprovalsQuery   =  $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id',$id],['approval',1]])->get();
                $totalRecipientApprovals    =   $totalRecipientApprovalsQuery->count();
                $totalRecipientApproved     = $totalRecipientApprovalsQuery->where('status',1)->count();

                $approvedUsers   =   array();
                $nonApprovedUsers   =   array();
                foreach ($totalRecipientApprovalsQuery as $recipientApproval){
                    if($recipientApproval->status==1) {
                        $approvedUsers[] = array('id' => $recipientApproval->emailUserInfo->id,
                            'userName' => $recipientApproval->emailUserInfo->email,
                            'time' => Carbon::parse($recipientApproval->updated_at)->format('d/m/Y h:i A')." AEDT");
                    }else{
                        $nonApprovedUsers[] = array('id' => $recipientApproval->emailUserInfo->id,
                            'userName' => $recipientApproval->emailUserInfo->email);
                    }
                }

                //email recipients
                $recipientsQuery  =    $sentDocket->first()->recipientInfo;
                $recipients =   array();
                foreach($recipientsQuery as $recipient):
                    $recipients[]     =   array('email' => $recipient->emailUserInfo->email,
                        'shareableLink' => url('docket/emailed',array($sentDocket->first()->encryptedID(),$recipient->encryptedID())));
                endforeach;

                $docketStatus   =   array('receivedTime'=> Carbon::parse($sentDocket->first()->created_at)->format('d/m/Y h:i A')." AEDT",
                    'status'=> $totalRecipientApproved."/".$totalRecipientApprovals,
                    'docketStatus'=> ($sentDocket->first()->status==1)?"Approved":"Sent",
                    'approvedUser' => $approvedUsers,
                    'docket_approval_type' =>$sentDocket->first()->docketApprovalType,
                    'nonApprovedUser' => $nonApprovedUsers,
                    'recipients' => $recipients);

                $userNotificationQuery  = $this->userNotificationRepository->getDataWhere([['type',5],['receiver_user_id',auth()->user()->id],['key',$id]]);
                if($userNotificationQuery->count()>0){
                    if($userNotificationQuery->first()->status==0){
                        $userNotificationQuery->update(['status'=>1]);
                    }
                }

                return response()->json(['docketStatus' => $docketStatus,
                                         'docketsValue' => $sentDocketValue,
                                         'docketApprovalType'=>$sentDocket->first()->docketApprovalType,
                                         'webView'=>$webView],200);
            } else {
                echo "not authorized";
            }
        else:
            return response()->json(['message' => 'Docket not found.'],500);
        endif;
    }

    public function getEmailDocketDetailsByIdWebView($request,$id){
        $sentDocket     = $this->emailSentDocketRepository->getDataWhere([['id',$id]])->first();
        $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
        $companyEmployeeQuery   =  $this->employeeRepository->getDataWhereIn('user_id',$recipientIds)->pluck('company_id');
        $empCompany    =   $this->companyRepository->getDataWhereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
        $adminCompanyQuery   =  $this->companyRepository->getDataWhereIn('user_id',$recipientIds)->pluck('id')->toArray();
        $company    = $this->companyRepository->getDataWhereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();

        $companyName = array();
        foreach ($company as $row){
            $companyName[] = $row->receiver_company_name;
        }
        $receiver_detail = array();
        foreach ($sentDocket->recipientInfo as $row){
            $receiver_detail[] =   $row->emailUserInfo->email;
        }
        $company_name =implode(", ", $companyName);
        $employee_name =implode(", ", $receiver_detail);
        $docketTimer = $this->sentDocketTimerAttachmentRepository->getDataWhere([['sent_docket_id',$id],['type',2]])->get();
        
        if($sentDocket->company_id == auth()->user()->companyInfo->id){
            $docketFields   = $this->emailSentDocketValueRepository->getDataWhere([['email_sent_docket_id',$sentDocket->id]])->get();
            if ($sentDocket->theme_document_id == 0){
                return (array( 'docket' => view('dashboard.company.docketManager.emailPreview',compact('sentDocket','docketFields','company','docketTimer','company_name','employee_name'))->render()));
            }else{
                $theme = $this->documentThemeRepository->getDataWhere([['id', $sentDocket->theme_document_id]])->first();
                return (array( 'docket' => view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentDocket','docketFields','company','docketTimer','company_name','employee_name'))->render()));
            }
        }else {
            flash(MessageDisplay::InvalidAction,'warning');
            return redirect()->back();
        }

    }
}