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


public function storecoordenates(Request $request)
{
    $barco = new Barco();
    $coordenates = $request->coordenates;

    foreach ($coordenates as $coordenate) {
        $barco->user_id = 1;
        $barco->user_barcos = 15;
        $barco->rival_id = 2;
        $barco->rival_barcos = 15;
        $barco->coordenate_user = json_encode($coordenate);
        $barco->coordenate_rival = json_encode($coordenate);
        $barco->save();
    }

    $response = [
        'success' => true,
        'message' => 'Coordenates stored successfully',
        'coordenates' => $coordenates
    ];

    return response()->json($response);
}

}
