<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use  App\Models\Barco;

class JuegosController extends Controller
{
    
public function descontarbarcos($id)
{
    $barcos = Barco::find($id);
    
}

}
