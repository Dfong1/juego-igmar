<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estadistica;

class EstadisticasController extends Controller
{

    public function sumapartidas($id)
    {
        $partidas_ganadas = Estadistica::where('user_id', $id)->where('partida', 1)->count();
        $partidas_perdidas = Estadistica::where('user_id', $id)->where('partida', 0)->count();
    
        return [
            'partidas_ganadas' => $partidas_ganadas,
            'partidas_perdidas' => $partidas_perdidas
        ];
    }

public function store(Request $request)
{
    $data = $request->all();

    
    $user_id = $data['ganador'] == 'user_id' ? $data['ganador'] : null;
    $rival_id = $data['ganador'] == 'rival_id' ? $data['ganador'] : null;
    $partida = $data['ganador'] == 'user_id' ? 1 : 0;

    
    Estadistica::create([
        'user_id' => $user_id,
        'rival_id' => $rival_id,
        'partida' => $partida
    ]); 
}




public function registrobatallas($id)
{
    $registros = Estadistica::where('user_id', $id)
        ->join('users', 'users.id', '=', 'estadisticas.rival_id')
        ->select('estadisticas.*', 'users.name as rival_name')
        ->get();

    return $registros;
}




}
