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
            // Verificar si el usuario ya está en un juego activo
            $userId = Auth::id();
            $existingGame = Game::where('status', 'activo')
                                ->where(function ($query) use ($userId) {
                                    $query->where('player1_id', $userId)
                                          ->orWhere('player2_id', $userId);
                                })
                                ->first();
    
            if ($existingGame) {
                return response()->json(['message' => 'Usuario ya está en un juego activo'], 400);
            }
    
            // Verificar si el usuario ya está en la cola de búsqueda
            $existingQueue = MatchPlayer::where('user_id', $userId)->first();
    
            if ($existingQueue) {
                return response()->json(['message' => 'El jugador ya está buscando partida']);
            }
    
            // Agregar al usuario a la cola de búsqueda
            $player = new MatchPlayer();
            $player->user_id = $userId;
            $player->save();
    
            // Inicializar $game
            $game = null;
    
          
            $usersInQueue = MatchPlayer::count();
    
            if ($usersInQueue >= 2) {
                // Obtener los jugadores emparejados en el orden en que se unieron
                $playerIds = MatchPlayer::orderBy('created_at')->take(2)->pluck('user_id');
                
                // Crear una nueva partida
                $game = Game::create([
                    'player1_id' => $playerIds[0],
                    'player2_id' => $playerIds[1],
                    'next_player_id' => $playerIds[1] // El primer turno es del player2
                ]);
    
                // Eliminar a los jugadores emparejados de la cola de búsqueda
                MatchPlayer::whereIn('user_id', $playerIds)->delete();
    
                // Emitir evento de partida creada y notificar a los jugadores emparejados
                event(new MatchPlayers($game->id));
            }
    
            // Verificar si se creó una partida
            if ($game) {
                event(new MatchPlayers($game->id));
                return response()->json(['message' => 'Buscando partida', 'game' => $game]);
            } else {
                event(new MatchPlayers($game->id));
                return response()->json(['message' => 'Buscando partida']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ha ocurrido un error al buscar partida'], 500);
        }
    }


    
}
