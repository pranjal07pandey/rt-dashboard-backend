<?php

namespace App\Http\Resources\V2\Message;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\V2\AmazoneBucket;
class MessaageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $name,$subtitle,$messagesGroups,$docket,$invoice,$emailSentDockets,$time;

    public function __construct($resource,$name,$subtitle,$messagesGroups,$docket,$invoice,$emailSentDockets,$time)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->name = $name;
        $this->subtitle = $subtitle;
        $this->messagesGroups = $messagesGroups;
        $this->docket = $docket;
        $this->invoice = $invoice;
        $this->emailSentDockets = $emailSentDockets;
        $this->time = $time;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type'      =>    $this->type,
            'sender'    =>  array('id' => $this->sender_user_id,
            'name'      =>  $this->name,
            'profile'   => ($this->type==5)?"": AmazoneBucket::url() . $this->senderDetails->image),
            'subtitle'  =>  $this->subtitle,
            'message'   =>   strip_tags($this->message),
            'messagesGroups' => $this->messagesGroups,
            'formattedMessage'   =>  $this->message,
            'key'       =>    $this->key,
            'docket'    =>  $this->docket,
            'invoice'    =>  $this->invoice,
            'emailSentDocket' => $this->emailSentDockets,
            'time'      =>  $this->time,
            'status'    =>  $this->status
        ];
    }
}
