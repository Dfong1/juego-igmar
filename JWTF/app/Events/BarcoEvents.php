<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BarcoEvents implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

     public $y;
     public $gameId;
     public $user_id;

     public $X;
     
    public function __construct($gameId, $user_id,$x,$y )
    {
        $this->gameId = $gameId;
        $this->user_id = $user_id;
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() 
    {
        return [
            new Channel('barcos.'.$this->gameId)
        ];
    }

    public function broadcastAs()
    {
    return 'BarcoEvents';
    }

  

}
