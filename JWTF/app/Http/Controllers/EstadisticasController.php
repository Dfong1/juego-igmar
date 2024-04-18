<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estadistica;
use App\Models\User;

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
    
        $user_id = $data['user_id'];
        $rival_id = $data['rival_id'];
        $partida = $data['ganador'] == 1 ? 1 : 0;
    
        Estadistica::create([
            'user_id' => $user_id,
            'rival_id' => $rival_id,
            'partida' => $partida
        ]); 
        Estadistica::create([
            'user_id' => $rival_id,
            'rival_id' => $user_id,
            'partida' => !$partida
        ]); 
    }




public function registrobatallas($id)
{
    $registros = Estadistica::where('user_id', $id)
        ->join('users', 'users.id', '=', 'estadisticas.rival_id')
        ->select('estadisticas.*', 'users.name as rival_name')
        ->get();

        $user = User::find($id);
        if (!$user) {
            return response()->json(["error" => "Usuario no encontrado"], 404);
        }

    return response()->json(["msg"=>"Registro de batallas encontradas","data"=>$registros],200);
}




}
