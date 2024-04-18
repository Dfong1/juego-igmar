<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class BarcosController extends Controller
{
    public function saveShipPositions(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);
        $game->ship_positions = $request->input('ship_positions');
        $game->save();

        return response()->json(['message' => 'Ship positions saved successfully']);
    }
}
