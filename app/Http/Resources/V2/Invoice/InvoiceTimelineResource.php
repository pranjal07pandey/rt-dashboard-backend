<?php

namespace App\Http\Resources\V2\Invoice;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use App\Helpers\V2\AmazoneBucket;
class InvoiceTimelineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $userName,$company,$invoiceStatus,$receiver;

    public function __construct($resource, $userName,$company,$invoiceStatus,$receiver=null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->userName = $userName;
        $this->company = $company;
        $this->invoiceStatus = $invoiceStatus;
        $this->receiver = $receiver;
    }

    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'companyInvoiceId' => 'rt-'.$this->company_id.'-inv-'.$this->company_invoice_id,
            'user_id'   =>  $this->user_id,
            'invoiceName' => $this->invoiceInfo->title,
            'sender' => $this->userName,
            'profile'=> AmazoneBucket::url() . $this->senderUserInfo->image,
            'company'   =>  $this->company,
            'dateAdded' =>  Carbon::parse($this->created_at)->format('d-M-Y'),
            'status'    => $this->invoiceStatus
        ];

        if($this->receiver){
            $response['receiver'] = $this->receiverInfo->email;
        }else{
            $response['recipients']    =  $this->receiverUserInfo->first_name. " ".$this->receiverUserInfo->last_name;
        }
        return $response;
    }
}
