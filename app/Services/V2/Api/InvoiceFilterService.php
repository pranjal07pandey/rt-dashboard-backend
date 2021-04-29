<?php
namespace App\Services\V2\Api;

use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\MessageDisplay;
use App\Helpers\V2\StaticValue;
use App\Http\Resources\V2\Invoice\InvoiceFilterDocketResource;
use App\Services\V2\ConstructorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Support\Collection;

class InvoiceFilterService extends ConstructorService {

    public  function getInvoiceDocketFilterParameter(Request $request){
        $userId = $request->record_time_user;
        $authCompany = auth()->user()->companyInfo;
        $totalSentDocketID  =    array();
        $receiverCompanyId  = FunctionUtils::getCompanyId($userId);
        $receiverCompanyUserId  =  FunctionUtils::getCompanyAllUserId($receiverCompanyId);
        $sentDocketQueryTemp    =  $this->sentDocketsRepository->getDataWhere([['sender_company_id',$authCompany->id],['invoiceable',1]])->orderBy('id','desc')->get();
        foreach($sentDocketQueryTemp as $sentDocket){
            if($sentDocket->sentDocketRecipientApproval){
                foreach ($sentDocket->sentDocketRecipientApproval as $approvalUserID){
                    $status     =    true;
                    if(!in_array($approvalUserID->user_id,$receiverCompanyUserId)){
                        $status =   false;
                        break;
                    }
                }
            }
            if($status){
                $totalSentDocketID[]    =   $sentDocket->id;
            }
        }
        $sentDocketQuery    =  $this->sentDocketsRepository->getDataWhereIn('id',$totalSentDocketID);

        if($sentDocketQuery->count()>0) {
            $resultQuery = $sentDocketQuery->orderBy('created_at', 'desc')->get();

            $docketName = array();
            $amounts = array();

            foreach ($resultQuery as $result) {
                $docketName[] = array(
                    'id'=>$result->docketInfo->id,
                    'title' => $result->docketInfo->title
                );
                $invoiceAmount  =    0;
                $invoiceAmountQuery    =   $this->sentDocketInvoiceRepository->getDataWhere([['sent_docket_id',$result->id],['type',2]])->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                    if(is_numeric($unitRate[0]["value"])){
                        $unitRate1= $unitRate[0]["value"];
                    }else{
                        $unitRate1=0;
                    }
                    if(is_numeric($unitRate[1]["value"])){
                        $unitRate2= $unitRate[1]["value"];
                    }else{
                        $unitRate2= 0;
                    }
                    $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
                }
                $amounts[] = $invoiceAmount;
            }
            $uniqueDocketName = FunctionUtils::unique_multidim_array($docketName,'id');
            $doc = array();
            foreach ($uniqueDocketName as $uniqueDocketNames){
                $doc[] = array(
                    'id'=>$uniqueDocketNames['id'],
                    'title' => $uniqueDocketNames['title']
                );
            }
            $data = array();
            $data['docket_template'] = $doc;
            $data['range'] = array(
                'min'=>min($amounts),
                'max'=>max($amounts));
            return response()->json($data);
        }
    }

    public function filterInvoiceableDocket($request){
        $userId = $request->record_time_user;
        $authCompany = auth()->user()->companyInfo;
        $authUserId = auth()->user()->id;
        $totalSentDocketID  =   array();

        //get company superadmin, admins user id
        $admin  =   array();
        $admin    =  $this->employeeRepository->getDataWhere([['company_id',$authCompany->id],['is_admin',1],['employed',1]])->pluck('user_id')->toArray();
        $admin[]   =   $authCompany->user_id;

        if(in_array($authUserId,$admin)){
            $sentDocketQueryTemp = $this->sentDocketsRepository->getDataWhere([['sender_company_id',$authCompany->id],['invoiceable',1]])->orderBy('id','desc')->get();
        }else{
            $sentDocketQueryTemp =  $this->sentDocketsRepository->getDataWhere([['user_id',$authUserId],['invoiceable',1]])->orderBy('id','desc')->get();
        }
        foreach($sentDocketQueryTemp as $sentDocket){
            if ($sentDocket->recipientInfo->count() == 1){
                if ($sentDocket->recipientInfo->first()->user_id == $userId) {
                    $totalSentDocketID[] = $sentDocket->id;
                }
            }else if($sentDocket->recipientInfo->count()>=2){
                //get all recipients by sent dockets id
                $tempSentDocketRecipient    =    $sentDocket->recipientInfo->pluck('user_id')->toArray();
                if (FunctionUtils::array_equal($tempSentDocketRecipient,array($authUserId,$userId))) {
                    $totalSentDocketID[]    =   $sentDocket->id;
                }
            }
        }
        $sentDocketQuery    =  $this->sentDocketsRepository->getDataWhereIn('id',$totalSentDocketID)->with('docketInfo','senderUserInfo','recipientInfo');
        if(Input::has("to")){
            if($request->from ){
                $sentDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
            if($request->to ){
                $sentDocketQuery->whereDate('created_at','<=',Carbon::parse($request->to )->format('Y-m-d'));
            }
        }else{
            if($request->from ){
                $sentDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
        }

        if($request->docket_template_id && $request->docket_template_id != null){
          $sentDocketQuery->whereIn('docket_id',$request->docket_template_id);
        }

        if($request->sent_docket_id && $request->sent_docket_id != null){
            $sentDocketQuery->where('id',$request->sent_docket_id);
        }

        $rangeValue = array();
        if ($request->min_amount!= '' && $request->min_amount != ''&& Input::has('min_amount') && Input::has('max_amount')){
            $range = array();
            foreach ($sentDocketQuery->get()  as $sentDocketQuerys  ){
                if ($sentDocketQuerys->sender_company_id == $authCompany->id){
                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    = $this->sentDocketInvoiceRepository->getDataWhere([['sent_docket_id',$sentDocketQuerys->id],['type',2]])->get();
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                        if(is_numeric($unitRate[0]["value"])){
                            $unitRate1= $unitRate[0]["value"];
                        }else{
                            $unitRate1=0;
                        }
                        if(is_numeric($unitRate[1]["value"])){
                            $unitRate2= $unitRate[1]["value"];
                        }else{
                            $unitRate2= 0;
                        }
                        $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
                    }
                    $range[] = ['docket_id'=>$sentDocketQuerys->docketInfo->id,'amount'=> $invoiceAmount];
                }
            }
            $rangeData = new Collection($range);

            if ($request->min_amount != '' && $request->max_amount != ''){
                foreach ($rangeData as $rangeDatas){
                    if ($rangeDatas['amount'] >= $request->min_amount && $rangeDatas['amount'] <= $request->max_amount){
                        $rangeValue[] = $rangeDatas['amount'];
                    }
                }
            }
        }

        $filterData    =   $sentDocketQuery->orderBy('created_at', 'desc')->get();
        $invoiceableDockets =   array();
        foreach ($filterData as $result){
            if ($result->sender_company_id == $authCompany->id):
                $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                $senderImage = $result->senderUserInfo->image;
                $company = $result->senderCompanyInfo->name;
                $recipientsQuery    =   $result->recipientInfo;
                $recipientData      =   "";
                foreach($recipientsQuery as $recipient) {
                    if($recipient->id==$recipientsQuery->first()->id)
                        $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                    else
                        $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                }
                //approval text
                $totalRecipientApprovals    =   $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]])->count();
                $totalRecipientApproved     =   $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id],['status',1]])->count();

                //check is approval
                $isApproval                 =   0;
                $isApproved                 =   0;

                if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id],['user_id',$authUserId]])->count() == 1){
                    $isApproval             =   1;
                    //check is approved
                    if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id],['user_id',$authUserId],['status',1]])->count() == 1){
                        $isApproved             =   1;
                    }
                }
                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }
                $invoiceDescription     =    array();
                $sentDocketInvoiceData = $this->sentDocketInvoiceRepository->getDataWhere([['sent_docket_id',$result->ids]])->get();
                $invoiceDescriptionQuery    = $sentDocketInvoiceData->where('type',1);
                foreach($invoiceDescriptionQuery as $description){
                    $invoiceDescription[]   =   array('label'=> $description->sentDocketValueInfo->label,'value' => $description->sentDocketValueInfo->value);
                }
                $invoiceAmount  =    0;
                $invoiceAmountQuery    = $sentDocketInvoiceData->where('type',2);
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                    if(is_numeric($unitRate[0]["value"])){
                        $unitRate1= $unitRate[0]["value"];
                    }else{
                        $unitRate1=0;
                    }
                    if(is_numeric($unitRate[1]["value"])){
                        $unitRate2= $unitRate[1]["value"];
                    }else{
                        $unitRate2= 0;
                    }
                    $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
                }
                $appendData = false;
                if (!Input::has('min_amount') && !Input::has('max_amount')){
                    $appendData = true;
                    
                }else{
                    if ( in_array($invoiceAmount,array_unique($rangeValue))){
                        $appendData = true;
                    }
                }
                if($appendData == true){
                    $invoiceableDockets[] = new InvoiceFilterDocketResource($result,'InvoiceDocket',$userName,$company,$invoiceDescription,$invoiceAmount,$recipientData,$senderImage,$approvalText,$isApproval,$isApproved);
                }
                empty($invoiceDescription);
                empty($invoiceAmount);
            endif;
        }
        return $invoiceableDockets;
    }

    public  function getInvoiceEmailDocketFilterParameter(Request $request){
        $authCompany = auth()->user()->companyInfo;
        $authUserId = auth()->user()->id;
        $emailSentDocket = $this->emailSentDocketRepository->getDataWhere([['user_id',$authUserId],['company_id',$authCompany->id]])->get();
        $arrays = array();
        foreach ($emailSentDocket as $row){
            foreach ($row->recipientInfo as $items){
                if ($items->email_user_id == $request->record_time_user){
                    $arrays[]=$items->email_sent_docket_id;
                }
            }
        }
        $matchEmailDocket = $this->emailSentDocketRepository->getDataWhereIn('id',$arrays);
        if($matchEmailDocket->count()>0) {
            $docketDetail = array();
            $amounts = array();
            $resultQuery = $matchEmailDocket->orderBy('created_at', 'desc')->get();
            foreach ($resultQuery as $result) {
                $docketDetail[] = array(
                    'id'=>$result->docketInfo->id,
                    'title' => $result->docketInfo->title
                );

                $invoiceAmount  =    0;
                $invoiceAmountQuery    = $this->sentEmailDocketInvoiceRepository->getDataWhere([['email_sent_docket_id',$result->id],['type',2]])->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];

                }
                $amounts[] = $invoiceAmount;
            }
            $uniqueDocketName = FunctionUtils::unique_multidim_array($docketDetail,'id');
            $doc = array();
            foreach ($uniqueDocketName as $uniqueDocketNames){
                $doc[] = array(
                    'id'=>$uniqueDocketNames['id'],
                    'title' => $uniqueDocketNames['title']
                );
            }
            $data = array();
            $data['docket_template'] = $doc;
            $data['range'] = array(
                'min'=>min($amounts),
                'max'=>max($amounts));
            return response()->json($data);
        }
    }

    public  function filterInvoiceableEmailDocket(Request $request){
        $authCompany = auth()->user()->companyInfo;
        $authUserId = auth()->user()->id;
        $userId = $request->record_time_user;
        $emailSentDocket = $this->emailSentDocketRepository->getDataWhere([['user_id',$authUserId],['company_id',$authCompany->id]])->with('recipientInfo')->get();
        $arrays = array();
        foreach ($emailSentDocket as $row){
            foreach ($row->recipientInfo as $items){
                if ($items->email_user_id == $userId){
                    $arrays[] = $items->email_sent_docket_id;
                }
            }
        }

        $sentEmailDocketQuery = $this->emailSentDocketRepository->getDataWhereIn('id',$arrays);
        if(Input::has("to")){
            if($request->from ){
                $sentEmailDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
            if($request->to ){
                $sentEmailDocketQuery->whereDate('created_at','<=',Carbon::parse($request->to )->format('Y-m-d'));
            }
        }else{
            if($request->from ){
                $sentEmailDocketQuery->whereDate('created_at','==',Carbon::parse($request->from)->format('Y-m-d'));
            }
        }

        if($request->docket_template_id && $request->docket_template_id != null){
            $sentEmailDocketQuery->whereIn('docket_id',$request->docket_template_id);
        }

        if($request->sent_docket_id && $request->sent_docket_id != null){
            $sentEmailDocketQuery->where('id',$request->sent_docket_id);
        }


        $rangeValue = array();
        if ($request->min_amount!= '' && $request->min_amount != ''&& Input::has('min_amount') && Input::has('max_amount')){
            $range = array();
            foreach ($sentEmailDocketQuery->get()  as $sentDocketQuerys  ){
                $invoiceAmount  =    0;
                $invoiceAmountQuery    =  $this->sentEmailDocketInvoiceRepository->getDataWhere([['email_sent_docket_id',$sentDocketQuerys->id],['type',2]])->with('sentEmailDocketValueInfo.sentDocketUnitRateValue')->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                }
                $range[] = ['docket_id'=>$sentDocketQuerys->docketInfo->id, 'amount'=> $invoiceAmount];
            }
            $rangeData = new Collection($range);

            if ($request->min_amount != '' && $request->max_amount != ''){
                foreach ($rangeData as $rangeDatas){
                    if ($rangeDatas['amount'] >= $request->min_amount && $rangeDatas['amount'] <= $request->max_amount){
                        $rangeValue[] = $rangeDatas['amount'];
                    }
                }
            }
        }

        $filterData    =   $sentEmailDocketQuery->orderBy('created_at', 'desc')->get();
        $invoiceableEmailDockets =   array();
        foreach ($filterData as $result) {
            if ($result->company_id == $authCompany->id):
                $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                $company = $result->senderCompanyInfo->name;
                $senderImage = $result->senderCompanyInfo->userInfo->image;
                $recipientName  =    "";
                foreach($result->recipientInfo as $recipient) {
                    if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                        $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                    }else{
                        $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                    }
                    if ($result->recipientInfo->count() > 1)
                        if ($result->recipientInfo->last()->id != $recipient->id){
                            $recipientName  = $recipientName.", ";
                        }

                }
                //approval text
                $totalRecipientApprovals    =  $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id',$result->id],['approval',1]])->count();
                $totalRecipientApproved     =  $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id',$result->id],['approval',1],['status',1]])->count();
                // $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }


                $invoiceDescription     =    array();
                $sentEmailDocketInvoiceData = $this->sentEmailDocketInvoiceRepository->getDataWhere([['email_sent_docket_id',$result->id]])->get();
                $invoiceDescriptionQuery    =    $sentEmailDocketInvoiceData->where('type',1);
                foreach($invoiceDescriptionQuery as $description){
                    $invoiceDescription[]   =   array('label'=> $description->sentEmailDocketValueInfo->label,'value' => $description->sentEmailDocketValueInfo->value);
                }
                $invoiceAmount  =    0;
                $invoiceAmountQuery    =    $sentEmailDocketInvoiceData->where('type',2);
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                }
                $appendData = false;
                if (!Input::has('min_amount') && !Input::has('max_amount')){
                    $appendData = true;
                }else{
                    if ( in_array($invoiceAmount,array_unique($rangeValue))){
                        $appendData = true;
                    }
                }
                if($appendData){
                    $isApproved = $totalRecipientApproved;
                    $isApproval = $totalRecipientApprovals;
                    $invoiceableEmailDockets[] = new InvoiceFilterDocketResource($result,'InvoiceEmailDocket',$userName,$company,$invoiceDescription,$invoiceAmount,$recipientName,$senderImage,$approvalText,$isApproval,$isApproved);
                }
                empty($invoiceDescription);
                empty($invoiceAmount);
            endif;
        }
        return $invoiceableEmailDockets;
    }
}