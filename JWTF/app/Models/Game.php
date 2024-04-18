<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'status',
        'player1_id',
        'player2_id',
        'winner_id',
        'next_player_id'
    ];
}
