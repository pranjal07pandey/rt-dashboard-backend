<?php

namespace App\Http\Resources\V2\Docket;
use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchDocketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $userId,$searchFor,$userName,$profile,$company,$docketStatus,$recipient,$approvalText,$isApproval,$isApproved,
        $receiver,$typeCheck,$recipientName;

    public function __construct($resource,$searchFor,$userId=null,$userName=null,$profile=null,$company=null,$docketStatus=null,
        $recipient=null,$approvalText=null,$isApproval=null,$isApproved=null,$receiver=null,$typeCheck=null,$recipientName=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->searchFor = $searchFor;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->profile = $profile;
        $this->company = $company;
        $this->docketStatus = $docketStatus;
        $this->recipient = $recipient;
        $this->approvalText = $approvalText;
        $this->isApproval = $isApproval;
        $this->isApproved = $isApproved;
        $this->receiver = $receiver;
        $this->typeCheck = $typeCheck;
        $this->recipientName = $recipientName;
    }

    public function toArray($request)
    {
        if($this->searchFor == 'emailInvoice'){
            $response = [
                'id' => $this->id,
                'user_id' => $this->receiver_user_id,
                'invoiceName' => $this->invoiceInfo->title,
                'receiver' => $this->receiverInfo->email,
                'company' => "",
                'dateAdded' => Carbon::parse($this->created_at)->format('d-M-Y'),
                'dateSorting' =>  Carbon::parse($this->created_at)->format('d-M-Y h:i:s'),
                'status' => $this->docketStatus
            ];
        }else{
            $response = [
                'id' => $this->id,
                'user_id'   =>  $this->userId,
                'sender' => $this->userName,
                'profile' => $this->profile,
                'company'   =>  $this->company,
                'dateAdded' =>  Carbon::parse($this->created_at)->format('d-M-Y'),
                'dateSorting' =>  Carbon::parse($this->created_at)->format('d-M-Y h:i:s'),
                'status'    => $this->docketStatus
            ];

            if($this->searchFor == 'invoice'){
                $response['invoiceName'] = $this->invoiceInfo->title;
                $response['receiver'] = $this->receiver;
                ($this->typeCheck != null) ? $response['companyInvoiceId'] = $this->formatted_id : '' ;
            }else{
                $response['docketName'] = $this->docketInfo->title;
                $response['recipient'] = $this->recipient;
            }

            if($this->searchFor == 'Emaildocket'){
                $response['name'] = $this->recipientName;
            }

            if($this->searchFor == 'docket'){
                $response['approvalText']  =  $this->approvalText;
                $response['isApproval']    =  $this->isApproval;
                $response['isApproved']    =  $this->isApproved;
            }
        }

        return $response;
    }
}
