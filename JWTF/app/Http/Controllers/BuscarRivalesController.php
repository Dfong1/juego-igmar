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
           
            $userId = Auth::id();
            $existingQueue = MatchPlayer::where('user_id', $userId)->first();
    
            if ($existingQueue) {
                return response()->json(['message' => 'Already in matchmaking queue']);
            }
    
            MatchPlayer::create(['user_id' => $userId]);
    
          
            $usersInQueue = MatchPlayer::count();
    
            if ($usersInQueue >= 2) {
                $playerIds = MatchPlayer::inRandomOrder()->take(2)->pluck('user_id');
                $game = Game::create(['player1_id' => $playerIds[0], 'player2_id' => $playerIds[1]]);
  
                MatchPlayer::whereIn('user_id', $playerIds)->delete();
    
                broadcast(new CrearJuego($game));
            }
    
            return response()->json(['message' => 'Joined matchmaking queue']);
        } catch (\Exception $e) {
    
            return response()->json(['error' => 'An error occurred while joining matchmaking queue'], 500);
        }
    }
    
}
