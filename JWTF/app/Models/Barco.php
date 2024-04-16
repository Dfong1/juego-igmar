<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barco extends Model
{
    use HasFactory;
    protected $table = 'barcos';

    protected $fillable=[
        'user_id',
        'user_barcos',
        'rival_id',
        'rival_barcos,'
    ];
}
