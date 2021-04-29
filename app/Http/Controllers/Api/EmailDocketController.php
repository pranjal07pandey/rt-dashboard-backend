<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\DocketFieldFooter;
use App\DocumentTheme;
use App\EmailSentDocket;
use App\EmailSentDocketRecipient;
use App\EmailSentDocketValue;
use App\Employee;
use App\SentDcoketTimerAttachment;
use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\V2\AmazoneBucket;

class EmailDocketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $sentDocket     =   EmailSentDocket::where('id',$id);
        $webView = $this->getEmailDocketDetailsByIdWebView($request, $id);
        if($sentDocket->count()==1):
            //check docket associated with user or not
            $companyId  =    $request->header('companyId');
            if($sentDocket->where('company_id',$companyId)->count()>0){
                $sentDocketValueQuery    =    EmailSentDocketValue::where('email_sent_docket_id',$id)->get();
                $sentDocketValue    = array();
                foreach ($sentDocketValueQuery as $row){
                    $subFiled   =   [];
                    if($row->docketFieldInfo->docket_field_category_id==7):
                        foreach($row->sentDocketUnitRateValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'label' => $subFiledRow->docketUnitRateInfo->label,
                                'value' => $subFiledRow->value);
                        endforeach;
                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => $row->value,
                            'subFiled' => $subFiled);
                        unset($subFiled);
                    elseif($row->docketFieldInfo->docket_field_category_id == 9):

                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;

                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => AmazoneBucket::url() . $row->value,
                            'subFiled' => $subFiled);

                    elseif($row->docketFieldInfo->docket_field_category_id==14):

                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;

                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => AmazoneBucket::url() . $row->value,
                            'subFiled' => $subFiled);
                    elseif($row->docketFieldInfo->docket_field_category_id==5):
                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;

                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => AmazoneBucket::url() . $row->value,
                            'subFiled' => $subFiled);
                    elseif ($row->docketFieldInfo->docket_field_category_id==13):
                        $footerDetails = DocketFieldFooter::select('id', 'value')->where("field_id", $row->docket_field_id)->orderBy('id', 'asc')->first();
                        $footers = array('id' => $row->id,
                            'docket_field_category_id' => $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' => $row->label,
                            'label' => $row->label,
                            'value' => @$footerDetails->value,
                            'subField' => array());

                    elseif($row->docketFieldInfo->docket_field_category_id==15):
                        foreach($row->sentEmailAttachment as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'name'=>$subFiledRow->name,
                                'url' => AmazoneBucket::url() . $subFiledRow->url);
                        endforeach;
                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => AmazoneBucket::url() . $row->url,
                            'subFiled' => $subFiled);
                    else:
                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => $row->value,
                            'subFiled' => $subFiled);
                    endif;
                }
                if(@$footers){
                    $sentDocketValue[] =   $footers;
                }

                //approval text
                $totalRecipientApprovalsQuery   =   EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('approval',1)->get();
                $totalRecipientApprovals    =   $totalRecipientApprovalsQuery->count();
                $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('approval',1)->where('status',1)->count();

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


                $docketStatus   =   array('receivedTime'=> Carbon::parse(EmailSentDocket::where('id',$id)->first()->created_at)->format('d/m/Y h:i A')." AEDT",
                    'status'=> $totalRecipientApproved."/".$totalRecipientApprovals,
                    'docketStatus'=> (EmailSentDocket::where('id',$id)->first()->status==1)?"Approved":"Sent",
                    'approvedUser' => $approvedUsers,
                    'docket_approval_type' =>$sentDocket->first()->docketApprovalType,
                    'nonApprovedUser' => $nonApprovedUsers,
                    'recipients' => $recipients);

                $userNotificationQuery  =   UserNotification::where('type',5)->where('receiver_user_id',$request->header('userId'))->where('key',$id);
                if($userNotificationQuery->count()>0){
                    if($userNotificationQuery->first()->status==0){
                        UserNotification::where('type',5)->where('receiver_user_id',$request->header('userId'))->where('key',$id)->update(['status'=>1]);
                    }
                }


                return response()->json(array('status' => true,
                                            'docketStatus' => $docketStatus,
                                            'docketsValue' => $sentDocketValue,
                                            'docketApprovalType'=>$sentDocket->first()->docketApprovalType,
                                            'webView'=>$webView));
            } else {
                echo "not authorized";
            }
        else:
            return response()->json(array('status' => false,'message' => 'Docket not found.'));
        endif;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getEmailDocketDetailsByIdWebView(Request $request,$id){
        $sentDocket     =   EmailSentDocket::findOrFail($id);
        $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
        $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
        $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
        $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
        $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
        if ($sentDocket->theme_document_id == 0){
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
            $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',2)->get();
            if($sentDocket->company_id==$request->header('companyId')){
                $docketFields   =  EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
//            return view('dashboard.company.docketManager.emailPreview',compact('sentDocket','docketFields','company','docketTimer'));
                return (array( 'docket' => view('dashboard.company.docketManager.emailPreview',compact('sentDocket','docketFields','company','docketTimer','company_name','employee_name'))->render()));

            }else {
                flash('Invalid action ! Please try with valid action.','warning');
                return redirect()->back();
            }
        }else{

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
            $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',2)->get();
            $theme = DocumentTheme::where('id', $sentDocket->theme_document_id)->first();
            if($sentDocket->company_id==$request->header('companyId')){
                $docketFields   =  EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
//            return view('dashboard.company.docketManager.emailPreview',compact('sentDocket','docketFields','company','docketTimer'));
                return (array( 'docket' => view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentDocket','docketFields','company','docketTimer','company_name','employee_name'))->render()));
            }else {
                flash('Invalid action ! Please try with valid action.','warning');
                return redirect()->back();
            }
        }

    }
}
