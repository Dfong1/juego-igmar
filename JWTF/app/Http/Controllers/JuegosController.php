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
    $user = auth()->user();
    $rival_id = $request->rival_id;
  

    $barco = new Barco();
    $coordenates = $request->coordenates;

    foreach ($coordenates as $coordenate) {
        $barco->user_id = $user->id;
        $barco->user_barcos = 15;
        $barco->rival_id = $rival_id;
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



public function bombardear($id, $barcotumbado, $latitud, $longitud)
{
    $barco = Barco::find($id);

    if ($barcotumbado) {
        $coordenateToFind = json_encode([$latitud, $longitud]);
        if ($barco && in_array($coordenateToFind, json_decode($barco->coordenate_user, true))) {
            $barco->user_barcos -= 1;
            $barco->coordenate_user = json_encode(array_values(array_diff(json_decode($barco->coordenate_user, true), [$coordenateToFind])));
        }
    } else {
        $coordenateToFind = json_encode([$latitud, $longitud]);
        if ($barco && in_array($coordenateToFind, json_decode($barco->coordenate_rival, true))) {
            $barco->rival_barcos -= 1;
            $barco->coordenate_rival = json_encode(array_values(array_diff(json_decode($barco->coordenate_rival, true), [$coordenateToFind])));
        }
    }

    $barco->save();

    return response()->json(['message' => 'Bombardeo realizado con éxito']);
}



public function turnos(Request $request)
{
    $coordenates = $request->json()->all(); 
      $user = auth()->user();
    $barco = Barco::where($user)->latest()->first(); 

    $coordenatesJson = json_encode($coordenates);

    if ($barco && $coordenatesJson === $barco->coordenate_user) {
        $coordenatesArray = json_decode($coordenatesJson, true);
        
        foreach ($coordenatesArray as $coordenate) {
            $coordenateToFind = json_encode($coordenate);
            if (in_array($coordenateToFind, $barco->user_barcos)) {
                $barco->user_barcos = array_values(array_diff($barco->user_barcos, [$coordenateToFind])); 
                $barco->save();
                break; 
            }
        }
        $turno = true; 
    } else {
        $turno = false; 
    }

    return response()->json(['turno' => $turno]);
}




}
