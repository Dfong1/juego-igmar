<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MatchPlayers;

use App\Models\MatchPlayer;



class BuscarRivalesController extends Controller
{
    public function buscar(Request $request)
    {
        
     $player = MatchPlayer::all();
     event(new MatchPlayers($player));
    }


    public function post()
    {
        $player=auth()->user();
        $matchplayer = new MatchPlayer();
        $matchplayer->user_id = $player->id;
        $matchplayer->save();
        event(new MatchPlayers($player));
        return response()->json($player);
    }

    public function update()
    {
        $jugador = auth()->user();
        $player = MatchPlayer::latest()->first();
        
        if(!$player){{
            return response()->json(['error' => 'jugadores no encontrados']);
        }}  
        $player->rival_id = $jugador->id;
        $player->save();
        return response()->json($player);
    }
    
}
