<?php

namespace App\Http\Resources\V2\Invoice;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceTempleteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $invoiceFields;

    public function __construct($resource, $invoiceFields)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->invoiceFields = $invoiceFields;
    }

    public function toArray($request)
    {
        return [
            'invoice' => ['id'         =>  $this->id,
                            'title'     =>  $this->title,
                            'subTitle'  =>  $this->subTitle,
                            'gst'       =>  $this->gst,
                            'gst_label' =>  $this->gst_label,
                            'gst_value' =>  $this->gst_value
                        ],
            'invoice_field' => $this->invoiceFields,
        ];
    }
}
