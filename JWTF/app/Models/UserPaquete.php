<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaquete extends Model
{
    use HasFactory;
    public $timestamp=false;
    protected $table = 'UserPaquete';

    protected $fillable = ["user_id","paquete_id"];

    public function users()
    {
      return $this->belongsTo(User::class);
    }

}
