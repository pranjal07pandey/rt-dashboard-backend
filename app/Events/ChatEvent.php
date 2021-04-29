<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ChatEvent extends Event implements ShouldBroadcast {
    use SerializesModels;

    public $data;

    public function __construct($value) {
        $this->data = [
            'datas' => $value
        ];
    }

    public function broadcastOn() {
        return ['test-channel'];
    }

}