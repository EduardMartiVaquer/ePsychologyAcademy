<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClassCallMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $class_id;
    public $offer;
    public $answer;
    public $candidate;

    public function __construct($class_id, $offer = null, $answer = null, $candidate = null)
    {
        $this->class_id = $class_id;
        $this->offer = $offer;
        $this->answer = $answer;
        $this->candidate = $candidate;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('ClassCallChannel.'.$this->class_id);
    }
}
