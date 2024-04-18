<?php

namespace App\Http\Controllers;

use App\Models\Barco;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarcosController extends Controller
{
    public function saveShipPositions(Request $request, $gameId)
    {
        $game = Game::findOrFail($gameId);
        $game->ship_positions = $request->input('ship_positions');
        $game->save();

        return response()->json(['message' => 'Ship positions saved successfully']);
    }

    public function colocarBarcos(Request $request, $gameId)
    {
        try {
            // Obtener el juego
            $game = Game::findOrFail($gameId);
    
            // Verificar si el juego ya ha comenzado
            if ($game->status !== 'pendiente') {
                return response()->json(['error' => 'El juego ya ha comenzado'], 400);
            }
    
            // Verificar si es el turno del usuario actual
            $currentUser = Auth::user();
            if ($currentUser->id !== $game->player1_id && $currentUser->id !== $game->player2_id) {
                return response()->json(['error' => 'No estás autorizado para colocar barcos en este juego'], 403);
            }
    
            // Obtener las posiciones de los barcos desde la solicitud
            $shipPositions = $request->input('ship_positions');
    
            // Validar las posiciones de los barcos si es necesario
            // Ejemplo: asegurarse de que las posiciones sean válidas y no se superpongan
    
            // Crear un registro de barco para cada posición en la solicitud
            foreach ($shipPositions as $position) {
                Barco::create([
                    'game_id' => $game->id, // Asigna el ID del juego actual
                    'user_id' => $currentUser->id,
                    'horizontal' => $position['horizontal'],
                    'vertical' => $position['vertical']
                ]);
            }
    
            // Actualizar el estado del juego a "activo" si ambos jugadores han colocado sus barcos
            if (Barco::where('game_id', $gameId)->count() === 15 * 2) {
                $game->status = 'activo';
                $game->save();
            }
    
            return response()->json(['message' => 'Posiciones de los barcos colocadas exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
