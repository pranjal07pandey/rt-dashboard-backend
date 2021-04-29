<?php

namespace App\Http\Resources\V2\Message;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\V2\AmazoneBucket;
class MessaageListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected $groupProfile,$seen,$seen_user;
    public function __construct($resource,$seen,$seen_user)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->seen = $seen;
        $this->seen_user = $seen_user;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'user_id' => $this->user_id,
            'userName' => $this->userInfo->first_name . " " . $this->userInfo->last_name,
            'profile' => AmazoneBucket::url() . $this->userInfo->image,
            'date' => Carbon::parse($this->created_date)->format('d-M-Y'),
            'seen' => $this->seen,
            'seen_by' => $this->seen_user,
        ];
    }
}
