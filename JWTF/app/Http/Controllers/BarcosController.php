<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use App\Models\Barco;

class BarcosController extends Controller
{
    public function saveShipPositions(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);
        $game->ship_positions = $request->input('ship_positions');
        $game->save();

        return response()->json(['message' => 'Ship positions saved successfully']);
    }



    public function getBarcosCount()
    {
        $userId = auth()->id();
    
        $userBarcosCount = Barco::where('user_id', $userId)->count();
    
        $game = Game::where('jugador_id', $userId)->where('status', 'activo')->first();
        $rivalId = $game->ganador_id == $userId ? $game->jugador_id : $game->ganador_id;
        $rivalBarcosCount = Barco::where('user_id', $rivalId)->count();
    
        return response()->json([
            'user_barcos_count' => $userBarcosCount,
            'rival_barcos_count' => $rivalBarcosCount
        ]);
    }


}
