<?php

namespace App\Http\Resources\V2\User;
use Illuminate\Support\Facades\File;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\V2\AmazoneBucket;
class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $type;

    public function __construct($resource, $type)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->type = $type;
    }

    public function toArray($request)
    {
        return [
            'id' => ($this->type == 'company') ? 0 : $this->id,
            'user_id' => $this->user_id,
            'company_id' => ($this->type == 'company') ? $this->id : $this->company_id,
            'company_name' => ($this->type == 'company') ? $this->name : $this->companyInfo->name,
            'company_abn' => ($this->type == 'company') ? $this->abn : $this->companyInfo->abn,
            'company_address' => $this->address,
            'first_name' => $this->userInfo->first_name,
            'last_name' => $this->userInfo->last_name,
            'image' => (AmazoneBucket::fileExist(@$this->userInfo->image)) ? AmazoneBucket::url() . $this->userInfo->image : asset('assets/dashboard/images/logoAvatar.png'),
        ];
    }
}
