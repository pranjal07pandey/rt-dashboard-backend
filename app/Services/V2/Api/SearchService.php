<?php
namespace App\Services\V2\Api;

use App\Http\Resources\V2\Docket\SearchDocketResource;
use Carbon\Carbon;
use App\Services\V2\ConstructorService;
use App\Helpers\V2\AmazoneBucket;

class SearchService extends ConstructorService {

    function searchByKeywordDocket($request){
        $searchKey = $request->search;
        $company = auth()->user()->companyInfo;
        $employeeIds    =  $this->employeeRepository->getDataWhere([['company_id',$company->id]])->pluck('user_id');
        $employeeIds[]  =   $company->user_id;
        $receivedSentDocket  = $this->sentDocketRecipientRepository->getDataWhereIn('user_id',$employeeIds)->distinct('sent_docket_id')->pluck('sent_docket_id')->toArray();
        $sentDocket    =  $this->sentDocketsRepository->getDataWhere([['sender_company_id',$company->id]])->pluck('id')->toArray();
        $possibleSentDocketsID    =   $this->sentDocketsRepository->getDataWhereIn('id',array_unique(array_merge($receivedSentDocket,$sentDocket)))->pluck('id')->toArray();
        
        return $this->commonSearchFilter($this->sentDocketsRepository,$possibleSentDocketsID,$searchKey,'docket');
    }

    function searchByKeywordEmailDocket($request){
        $searchKey = $request->search;
        $company = auth()->user()->companyInfo;
        $possibleSentDocketsID    =  $this->emailSentDocketRepository->getDataWhere([['company_id',$company->id]])->orderBy('created_at','desc')->pluck('id')->toArray();

        return $this->commonSearchFilter($this->emailSentDocketRepository,$possibleSentDocketsID,$searchKey,'emailDocket');
    }

    function searchByKeywordInvoice($request){
        $searchKey = $request->search;
        $company = auth()->user()->companyInfo;
        $employeeIds    =   $this->employeeRepository->getDataWhere([['company_id',$company->id]])->pluck('user_id');
        $employeeIds[]  =   $company->user_id;
        $sentInvoiceIds = $this->sentInvoiceRepository->getDataWhereIn('user_id',$employeeIds)->pluck('id')->toArray();

        if(count($sentInvoiceIds) != 0){
            $totalSentInvoiceIds = $sentInvoiceIds;
        }

        return $this->commonSearchFilter($this->sentInvoiceRepository,$totalSentInvoiceIds,$searchKey,'invoice');
    }

    function searchByKeywordEmailInvoice($request){
        $searchKey = $request->search;
        $company = auth()->user()->companyInfo;
        $possibleSentDocketsID     =  $this->emailSentInvoiceRepository->getDataWhere('company_id',$company->id)->orderBy('created_at','desc')->pluck('id')->toArray();
        return $this->commonSearchFilter($this->emailSentInvoiceRepository,$possibleSentDocketsID,$searchKey,'emailInvoice');
    }

    public function commonSearchFilter($repository,$possibleSentDocketsID,$searchKey,$searchFor){
        $matchedIDArray     =   array();
        //check docket id
        $matchedIDArray =   $repository->getDataWhere([['id','like','%'.$searchKey.'%']])->whereIn('id',$possibleSentDocketsID)->pluck('id')->toArray();
        
        if(count($matchedIDArray)>0){
            $possibleSentDocketsID  =   array_merge(array_diff($possibleSentDocketsID,$matchedIDArray),array_diff($matchedIDArray,$possibleSentDocketsID));
        }
        $receiverDocketQuery    =    $repository->getDataWhereIn('id',$possibleSentDocketsID)->get();
        //check docket info(sender name, sender company name , receiver name, company name //
        foreach ($receiverDocketQuery as $row){
            $senderName =   $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name;
            $senderCompanyName  =   $row->senderCompanyInfo->name;
            if(preg_match("/".$searchKey."/i",$senderName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            if($searchFor == 'docket'){
                //receiver info
                $receiversName  =   "";
                //for receivers name
                if($row->recipientInfo){
                    $sn = 1;
                    foreach($row->recipientInfo as $recipient):
                        $receiversName  =   $receiversName.@$recipient->userInfo->first_name." ". @$recipient->userInfo->last_name;
                        if($sn!=$row->recipientInfo->count()):
                            $receiversName  =   $receiversName.", ";
                        endif;
                        $sn++;
                    endforeach;
                }
                //for receivers company name
                $recipientIds   =   $row->recipientInfo->pluck('user_id');
                $companyEmployeeQuery   =  $this->employeeRepository->getDataWhereIn('user_id',$recipientIds)->pluck('company_id');
                $empCompany    =  $this->companyRepository->getDataWhereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
                $adminCompanyQuery   =    $this->companyRepository->getDataWhereIn('user_id',$recipientIds)->pluck('id')->toArray();
                $company    =   $this->companyRepository->getDataWhereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();

                if(preg_match("/".$searchKey."/i",$receiversName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                    $matchedIDArray[]   =   $row->id;
                    continue;
                }
            }else if($searchFor == 'emailDocket' || $searchFor == 'emailInvoice'){
                //for receivers Email Company name Company address Company full name
                if($row->receiverUserInfo != null){
                    $receiverEmailed= $row->receiverUserInfo->email;
                    if(preg_match("/".$searchKey."/i",$receiverEmailed)){
                        $matchedIDArray[]   =   $row->id;
                        continue;
                    }
                }

                $receiverFullName = $row->receiver_full_name;
                $receiverCompanyAddress = $row->receiver_company_address;
                $receiverCompanyName = $row->receiver_company_name;

                if (preg_match("/".$searchKey."/i",$receiverFullName)){
                    $matchedIDArray[]   =   $row->id;
                    continue;
                }
                if (preg_match("/".$searchKey."/i",$receiverCompanyAddress)){
                    $matchedIDArray[]   =   $row->id;
                    continue;
                }
                if (preg_match("/".$searchKey."/i",$receiverCompanyName)){
                    $matchedIDArray[]   =   $row->id;
                    continue;
                }
            }else if($searchFor == 'invoice'){
                $receiverName=$row->receiverUserInfo->first_name." ".$row->receiverUserInfo->last_name;
                $receiverCompanyName  =   $row->senderCompanyInfo->name;
                if(preg_match("/".$searchKey."/i",$receiverName) || preg_match("/".$searchKey."/i",$receiverCompanyName)){
                    $matchedIDArray[]   =   $row->id;
                    continue;
                }
            }

            if($searchFor == 'invoice' || $searchFor == 'emailInvoice'){
                if(preg_match("/".$searchKey."/i",$row->invoiceInfo->title)){
                    $matchedIDArray[]   =   $row->id;
                    continue;
                }
            }

            if(preg_match("/".$searchKey."/i",Carbon::parse($row->created_at)->format('d-M-Y'))) {
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            if($searchFor == 'docket' || $searchFor == 'emailDocket'){
                if(preg_match("/".$searchKey."/i",$row->docketInfo->title)){
                    $matchedIDArray[]   =   $row->id;
                    continue;
                }
                //for docket field value
                if($row->sentDocketValue){
                    foreach ($row->sentDocketValue as $rowValue){
                        if($rowValue->docketFieldInfo != null){
                            if($rowValue->docketFieldInfo->docket_filed_category_id!=5 && $rowValue->docketFieldInfo->docket_filed_category_id!=7 && $rowValue->docketFieldInfo->docket_filed_category_id!=8 && $rowValue->docketFieldInfo->docket_filed_category_id!=9 &&
                                $rowValue->docketFieldInfo->docket_filed_category_id!=12 && $rowValue->docketFieldInfo->docket_filed_category_id!=13 && $rowValue->docketFieldInfo->docket_filed_category_id!=14){
                                if(preg_match("/".$searchKey."/i",$rowValue->value)) {
                                    $matchedIDArray[]   =   $row->id;
                                }
                            }
                        }
                    }
                }
            }
        }

        $sentDockets    =   $repository->getDataWhereIn('id',$matchedIDArray)->orderBy('created_at','desc')->get();
        $dockets = [];
        foreach($sentDockets as $result){
            if($searchFor == 'emailInvoice'){
                if ($result->status == 1)
                    $invoiceStatus = "Approved";
                else
                    $invoiceStatus = "Sent";

                $dockets[] = new SearchDocketResource($result,$searchFor,null,null,null,null,$invoiceStatus);
            }else{
                $userId  = 	$result->user_id;
                $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                $company    =   $result->senderCompanyInfo->name;

                if($result->user_id == auth()->user()->id){
                    if($searchFor == 'invoice'){
                        $userId  = 	$result->receiver_user_id;
                        $userName  =   $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name;
                        $profile    =  AmazoneBucket::url() . $result->receiverUserInfo->image;
                        $company    =   $result->receiverCompanyInfo->name;
                    }
                    if($result->status==0):
                        $docketStatus   =   "Sent";
                    endif;
                } else {

                    if($result->status==0):
                        $docketStatus   =   "Received";
                    endif;
                }

                if($result->status==1)
                    $docketStatus ="Approved";

                if($searchFor == 'docket' || $searchFor == 'emailDocket'){
                    $recipientsQuery    =   $result->recipientInfo;
                    $recipientData      =   "";
                    foreach($recipientsQuery as $recipient) {
                       
                        if($recipient->id==$recipientsQuery->first()->id)
                            $recipientData  =  ($searchFor == 'docket') ? $recipient->userInfo->first_name." ".$recipient->userInfo->last_name : $recipient->receiver_full_name;
                        else
                            $recipientData  =  ($searchFor == 'docket') ? $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name : $recipientData.", ".$recipient->receiver_full_name;
                    }
                }
                if($searchFor == 'docket'){
                    $sentDocketRecipientApprovalData = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]]);
                    //approval text
                    $totalRecipientApprovals    =   $sentDocketRecipientApprovalData->count();
                    $totalRecipientApproved     =   $sentDocketRecipientApprovalData->where('status',1)->count();

                    //check is approval
                    $isApproval                 =   0;
                    $isApproved                 =   0;
                    if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id],['user_id',auth()->user()->id]])->count()==1){
                        $isApproval             =   1;

                        //check is approved
                        if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id],['user_id',auth()->user()->id]])->where('status',1)->count()==1){
                            $isApproved             =   1;
                        }
                    }

                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                    $dockets[]   = new SearchDocketResource($result,$searchFor,$userId,$userName, AmazoneBucket::url() . $result->senderUserInfo->image,$company,$docketStatus,$recipient,$approvalText,$isApproval,$isApproved);
                }else if($searchFor == 'emailDocket'){
                    $dockets[]   = new SearchDocketResource($result,$searchFor,$userId,$userName, AmazoneBucket::url() . $result->senderUserInfo->image,$company,$docketStatus,$recipient);
                }else if($searchFor == 'invoice'){
                    $dockets[]   = new SearchDocketResource($result,$searchFor,$userId,$userName,$profile,$company,$docketStatus);
                }
            }
        }

        $size = count($dockets);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($dockets[$j+1]["dateSorting"]) > strtotime($dockets[$j]["dateSorting"])) {
                    $tempArray   =    $dockets[$j+1];
                    $dockets[$j+1] = $dockets[$j];
                    $dockets[$j]  =   $tempArray;
                }
            }
        }

        return $dockets;
    }
}