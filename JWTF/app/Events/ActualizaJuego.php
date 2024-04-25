<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActualizaJuego implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;
    public $shipsDestroyedByOpponent;
    public $shipsDestroyedByCurrentUser;

    public function __construct(Game $game, $shipsDestroyedByOpponent, $shipsDestroyedByCurrentUser)
    {
        $this->game = $game;
        $this->shipsDestroyedByOpponent = $shipsDestroyedByOpponent;
        $this->shipsDestroyedByCurrentUser = $shipsDestroyedByCurrentUser;
    }

    public function broadcastOn()
    {
        return new Channel('evento-juego');
    }

    public function broadcastAs()
    {
        return 'ActualizaJuego';
    }
}