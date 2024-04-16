<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LogHistorys implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $logs;

    public function __construct($logs)
    {
        $this->logs = $logs;
    }

    public function broadcastOn()
    {
        return new Channel('consumirlogs');
    }
}