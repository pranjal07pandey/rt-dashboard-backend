<?php

namespace App\Events;

use App\Timer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TimerChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $timer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Timer $timer)
    {
        $this->timer = $timer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('pizza-tracker.'.$this->order->id);
        return ['private-timer-tracker.'.$this->timer->id, 'timer-tracker'];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $extra = [
            'user_id' => $this->timer->user_id,
            'location' => $this->timer->location,
            'longitude' => $this->timer->longitude,
            'latitude' => $this->timer->latitude,
            'time_started' => $this->timer->time_started,
            'status' => $this->timer->status,
        ];

        return $extra;
    }
}
