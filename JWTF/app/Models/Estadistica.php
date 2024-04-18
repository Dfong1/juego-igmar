<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estadistica extends Model
{
    use HasFactory;

    protected $table = 'estadisticas';
    public $timestamps = false;

    protected $fillable=[
        'user_id',
        'partida',
        'rival_id',
    ];
}
