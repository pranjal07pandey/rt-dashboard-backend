<?php

namespace App\Http\Resources\V2\Invoice;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class InvoiceConversationChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $userName,$company,$invoiceStatus,$profile,$document_type;

    public function __construct($resource,$userName,$company,$invoiceStatus,$profile=null,$document_type)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->userName = $userName;
        $this->company = $company;
        $this->invoiceStatus = $invoiceStatus;
        $this->profile = $profile;
        $this->document_type = $document_type;
    }

    public function toArray($request)
    {
        $response = [
            'id' => $this->id,
            'user_id'   =>  $this->user_id,
            'invoiceName' => $this->invoiceInfo->title,
            'sender' => $this->userName,
            'company'   =>  $this->company,
            'dateAdded' =>  Carbon::parse($this->created_at)->format('d-M-Y'),
            'status'    => $this->invoiceStatus
        ];
        if($this->document_type == '2'){
            $response['profile'] = $this->profile;
            $response['dateSorting'] =  Carbon::parse($this->created_at)->format('d-M-Y h:i:s');
        }
        return $response;
    }
}
