<?php

namespace App\Http\Resources\V2\Invoice;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Helpers\V2\AmazoneBucket;
class InvoiceFilterDocketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $userName,$for,$company,$invoiceDescription,$invoiceAmount,$recipientData,$senderImage,$approvalText,
            $isApproval,$isApproved,$companyDocketId,$statusData,$canReject,$isReject,$is_cancel;

    public function __construct($resource, $for,$userName,$company,$invoiceDescription,$invoiceAmount,$recipientData,$senderImage,
                    $approvalText,$isApproval=null,$isApproved=null,$companyDocketId=null,$statusData=null,$canReject=null,$isReject=null,$is_cancel=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->for = $for;
        $this->userName = $userName;
        $this->company = $company;
        $this->invoiceDescription = $invoiceDescription;
        $this->invoiceAmount = $invoiceAmount;
        $this->recipientData = $recipientData;
        $this->senderImage = $senderImage;
        $this->approvalText = $approvalText;
        $this->isApproval = $isApproval;
        $this->isApproved = $isApproved;
        $this->companyDocketId = $companyDocketId;
        $this->statusData = $statusData;
        $this->canReject = $canReject;
        $this->isReject = $isReject;
        $this->is_cancel = $is_cancel;
    }

    public function toArray($request)
    {
         // $docketsOrInvoice[]   =   array('id' => $result->id,
                //     'user_id'   =>  $userId,
                //     'docketName' => $result->docketInfo->title,
                //     'sender' => $userName,
                //     'profile' => asset($result->senderUserInfo->image),
                //     'company'   =>  $company,
                //     'recipient' => $recipientData,
                //     'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                //     'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                //     'isApproved'    =>  $result->status,
                //     'approvalText'  =>  $approvalText,
                //     'isApproval'    =>  $isApproval,
                //     'isApproved'    =>  $isApproved,
                //     'status'    => $invoiceOrDocketStatus);
        $response = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'docketName' => $this->docketInfo->title,
            'sender' => $this->userName,
            'company' => $this->company,
            'dateAdded' => Carbon::parse($this->created_at)->format('d-M-Y'),
            'dateSorting' =>  Carbon::parse($this->created_at)->format('d-M-Y h:i:s'),
            'recipient'=>$this->recipientData,
            'status' => $this->approvalText,
            'isApproved'=>$this->isApproved,
        ];
        if($this->for == 'emailConversation'){
            $response["profile"] = AmazoneBucket::url() . $this->senderImage;
            $response["approvalText"]  = $this->approvalText;
            $response["companyDocketId"]  = $this->companyDocketId;
        }elseif($this->for == 'filterDocument'){
            $response["isApproval"]  = $this->isApproval;
            $response["approvalText"]  = $this->approvalText;
            $response["status"]  = $this->statusData;
            $response['profile'] = AmazoneBucket::url() . $this->senderImage;
        }else{
            if($this->for == 'invoiceLatestDocket'){
                $response["canReject"]  = $this->canReject;
                $response["isReject"]  = $this->isReject;
                $response["is_cancel"]  = $this->is_cancel;
                $response["status"]  = $this->statusData;
                $response['isApproval'] = $this->isApproval;
                $response["profile"] = AmazoneBucket::url() . $this->senderImage;
                $response["approvalText"]  = $this->approvalText;
                $response["companyDocketId"]  = $this->companyDocketId;
            }else if($this->for == 'docketFilter'){
                $response["docketId"]  = $this->docketInfo->id;
                $response["recipients"]  = $this->recipientData;
                $response["canReject"]  = $this->canReject;
                $response["isReject"]  = $this->isReject;
                $response["status"]  = $this->statusData;
                $response['isApproval'] = $this->isApproval;
                $response["profile"] = AmazoneBucket::url() . $this->senderImage;
                $response["approvalText"]  = $this->approvalText;
                $response["companyDocketId"]  = $this->companyDocketId;
            }else{
                $response['invoiceDescription'] = $this->invoiceDescription;
                $response['invoiceAmount'] = $this->invoiceAmount;
                $response['senderImage'] = AmazoneBucket::url() . $this->senderImage;
                $response['isApproval'] = $this->isApproval;
                $response['docketTemplateId'] = $this->docketInfo->id;
            }
        }

        if($this->for == 'invoiceDocketList'){
            $response["companyDocketId"]  = $this->companyDocketId;
        }

        if($this->for == 'InvoiceDocket'){
            $response['companyDocketId'] = 'rt-'.$this->sender_company_id.'-doc-'.$this->company_docket_id;
        }elseif($this->for == 'InvoiceEmailDocket'){
            $response['companyDocketId'] = 'rt-'.$this->company_id.'-edoc-'.$this->company_docket_id;
        }
        
        return $response;
    }
}
