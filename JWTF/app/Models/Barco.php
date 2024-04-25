<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barco extends Model
{
    use HasFactory;
    protected $table = 'barcos';

    protected $fillable=[
        'game_id',
        'user_id',
        'horizontal',
        'vertical'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
