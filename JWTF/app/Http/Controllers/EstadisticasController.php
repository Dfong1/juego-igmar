<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estadistica;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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




    public function registrobatallas()
    {
        $id = auth()->user()->id;

        $registros = Estadistica::select(
                'user_id', 
                'rival_id', 
                'partida', 
                'users.name as user_name', 
                'rivals.name as rival_name',
                'estadisticas.partida as winner_id', // Utilizar estadisticas.partida como winner_id
                \DB::raw('(SELECT COUNT(*) FROM barcos b1 WHERE b1.user_id = estadisticas.user_id AND b1.game_id = estadisticas.partida) as user_ships_remaining'),
                \DB::raw('(SELECT COUNT(*) FROM barcos b2 WHERE b2.user_id = estadisticas.rival_id AND b2.game_id = estadisticas.partida) as rival_ships_remaining')
            )
            ->join('users as rivals', 'estadisticas.rival_id', '=', 'rivals.id')
            ->join('users', 'estadisticas.user_id', '=', 'users.id')
            ->where('user_id', $id)
            ->orWhere('rival_id', $id)
            ->orderBy('estadisticas.id', 'DESC') // Ordenar por partida de forma ascendente
            ->get();
    
            foreach ($registros as $registro){
                if($registro->rival_id == $id){
                    $rival_name = $registro->rival_name;
                    $rival_id = $registro->rival_id;
                    $temp = $registro->user_ships_remaining;
                    $registro->rival_id = $registro->user_id;
                    $registro->rival_name = $registro->user_name;
                    $registro->user_id = $rival_id;
                    $registro->user_name = $rival_name;
                    // Intercambiar barcos restantes
                    $registro->user_ships_remaining = $registro->rival_ships_remaining;
                    $registro->rival_ships_remaining = $temp;
                }
            }
    
        return response()->json(["msg" => "Registro de batallas encontradas", "data" => $registros], 200);
    }




}
