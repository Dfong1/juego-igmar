<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchPlayer extends Model
{
    use HasFactory;
    protected $table = 'buscarrivales';

    protected $fillable=[
        'user_id'
    ];
}
