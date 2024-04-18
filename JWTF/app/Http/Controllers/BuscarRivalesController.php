<?php

namespace App\Http\Controllers;

use App\Events\CrearJuego;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Events\MatchPlayers;

use App\Models\MatchPlayer;
use Illuminate\Support\Facades\Auth;



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

    public function joinQueue(Request $request)
    {
        try {
            // Verificar si el usuario ya está en la cola de búsqueda
            $userId = Auth::id();
            $existingQueue = MatchPlayer::where('user_id', $userId)->first();
    
            if ($existingQueue) {
                return response()->json(['message' => 'Already in matchmaking queue']);
            }
    
            // Agregar al usuario a la cola de búsqueda
            MatchPlayer::create(['user_id' => $userId]);
    
            // Verificar si hay suficientes jugadores en la cola para emparejar
            $usersInQueue = MatchPlayer::count();
    
            if ($usersInQueue >= 2) {
                // Emparejar jugadores y crear una nueva partida
                $playerIds = MatchPlayer::inRandomOrder()->take(2)->pluck('user_id');
                $game = Game::create(['player1_id' => $playerIds[0], 'player2_id' => $playerIds[1]]);
    
                // Eliminar a los jugadores emparejados de la cola de búsqueda
                MatchPlayer::whereIn('user_id', $playerIds)->delete();
    
                // Emitir evento de partida creada y notificar a los jugadores emparejados
                broadcast(new CrearJuego($game));
            }
    
            return response()->json(['message' => 'Joined matchmaking queue']);
        } catch (\Exception $e) {
    
            return response()->json(['error' => 'An error occurred while joining matchmaking queue'], 500);
        }
    }
    
}
