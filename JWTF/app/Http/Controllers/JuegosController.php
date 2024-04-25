<?php

namespace App\Http\Controllers;

use App\Events\ActualizaJuego;
use App\Events\CambiarTurno;
use App\Events\BarcoEvents;
use App\Models\Estadistica;
use App\Models\Game;
use Illuminate\Http\Request;
Use  App\Models\Barco;
use Illuminate\Support\Facades\Auth;

class JuegosController extends Controller
{
    
public function descontarbarcos($id)
{
    $barcos = Barco::find($id);
}

// public function makeMove(Request $request, $gameId)
// {
//     // Lógica para validar y realizar el movimiento del jugador

//     // Cambiar el turno
//     $game = Game::findOrFail($gameId);
//     $game->juagador_id = $game->users->where('id', '!=', Auth::id())->first()->id;
//     $game->save();

//     broadcast(new CambiarTurno($game));

//     broadcast(new ActualizaJuego($game));

//     return response()->json(['message' => 'Move made successfully']);
// }

public function hacerMovimiento(Request $request, $gameId)
{
    try {
        // Obtener el juego
        $game = Game::find($gameId);

        // Verificar si el juego se encontró
        if (!$game) {
            return response()->json(['error' => 'El juego no se encontró'], 404);
        }

        // Obtener el usuario actual
        $currentUser = auth()->user();

        // Verificar si el usuario está autenticado
        if (!$currentUser) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // Validar la solicitud
        $this->validate($request, [
            'horizontal' => 'required|integer',
            'vertical' => 'required|integer',
        ]);

        // Verificar si el juego está en curso
        if ($game->status !== 'activo') {
            return response()->json(['error' => 'El juego no está en curso'], 400);
        }

        // Verificar si el usuario actual es un jugador en el juego
        if ($currentUser->id !== $game->player1_id && $currentUser->id !== $game->player2_id) {
            return response()->json(['error' => 'No estás autorizado para hacer movimientos en este juego'], 403);
        }

        // Verificar si es el turno del usuario actual
        if ($currentUser->id !== $game->next_player_id) {
            return response()->json(['error' => 'No es tu turno >:('], 403);
        }

        // Realizar el ataque y actualizar el estado del juego
        if ($currentUser->id == $game->player1_id) {
            // Realizar el ataque y actualizar el estado del juego
            $isSuccessful = $this->checkIfSuccessfulAttack($gameId, $request->horizontal, $request->vertical, $game->player2_id);
        } else {
            // Realizar el ataque y actualizar el estado del juego
            $isSuccessful = $this->checkIfSuccessfulAttack($gameId, $request->horizontal, $request->vertical, $game->player1_id);
        }

        // Verificar si el usuario actual ha ganado
        $winnerId = $this->checkForWinner($gameId, $currentUser->id);
        if ($winnerId) {
            Estadistica::create([
                'user_id' => $game->player1_id,
                'rival_id' => $game->player2_id,
                'partida' => $winnerId
            ]);
            $game->status = 'terminado';
            $game->winner_id = $winnerId;
            $game->save();
            event(new ActualizaJuego($game, 0, 0));

            return response()->json(['message' => '¡Felicidades! Has ganado']);
        }

        // Determinar el ID del siguiente jugador
        $nextPlayerId = $this->determineNextPlayer($game, $currentUser);

        // Guardar el ID del siguiente jugador en el juego
        $game->next_player_id = $nextPlayerId;
        $game->save();

        // Obtener el ID del jugador 2 del juego
        $player2Id = $game->player2_id;

        // Obtener el conteo de barcos derribados por cada jugador
        if ($isSuccessful) {
            if ($currentUser->id == $player2Id) {
                event(new ActualizaJuego($game, $isSuccessful, 0));
            } else {
                event(new ActualizaJuego($game, 0, $isSuccessful));
            }
        }

        // Emitir evento de actualización del juego con el conteo de barcos derribados por cada jugador

        return response()->json(['message' => 'Movimiento hecho con éxito', 'is_successful' => $isSuccessful ? 1 : 0]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


private function checkIfSuccessfulAttack($gameId, $x, $y, $user_id): bool {
    // Buscar el barco en las coordenadas especificadas
    $movimiento = Barco::where('game_id', $gameId)
                        ->where('horizontal', $x)
                        ->where('vertical', $y)
                        ->where('user_id', $user_id)
                        ->first();

    // Verificar si se encontró el barco
    if($movimiento){

        event(new BarcoEvents($gameId, $user_id,$x, $y, ));
        // Si se encontró el barco, eliminarlo
        $movimiento->delete();

        return 1;
    } else {
        // Si no se encontró el barco, el ataque no fue exitoso
        return 0;
    }
}

private function checkForWinner($gameId, $playerId)
{
    // Obtener el juego y los jugadores involucrados
    $game = Game::find($gameId);
    $opponentId = $playerId === $game->player1_id ? $game->player2_id : $game->player1_id;

    // Contar el número de barcos restantes del oponente
    $remainingShips = Barco::where('game_id', $gameId)
        ->where('user_id', $opponentId)
        ->count();

    // Si el oponente no tiene barcos restantes, el jugador actual gana
    if ($remainingShips === 0) {
        return $playerId;
    }

    return null; // Retornar null si no hay ganador aún
}



private function determineNextPlayer($game, $currentUser)
{
    // Determinar el ID del siguiente jugador
    if ($currentUser->id === $game->player1_id) {
        return $game->player2_id;
    } elseif ($currentUser->id === $game->player2_id) {
        return $game->player1_id;
    }

    return null; // Retornar null si el usuario actual no es un jugador válido en el juego
}

public function obtenerJuegoActual(Request $request)
{
    // Obtener el usuario autenticado
    $user = Auth::user();

    // Buscar el juego más reciente donde el usuario es jugador 1 o jugador 2
    $currentGame = $user->games()
                        ->where(function ($query) use ($user) {
                            $query->where('player1_id', $user->id)
                                  ->orWhere('player2_id', $user->id);
                        })
                        ->latest() // Obtener el juego más reciente basado en el ID
                        ->first();

    // Verificar si se encontró un juego y si está activo
    if ($currentGame && $currentGame->status !== 'terminado') {
        // Devolver los detalles del juego
        return response()->json(['game' => $currentGame]);
    } else {
        // Si no se encontró un juego activo, devolver un mensaje indicando eso
        return response()->json(['message' => 'Usuario no está en un juego activo'], 404);
    }
}



// public function bombardear($id, $barcotumbado, $latitud, $longitud)
// {
//     $barco = Barco::find($id);

//     if ($barcotumbado) {
//         $coordenateToFind = json_encode([$latitud, $longitud]);
//         if ($barco && in_array($coordenateToFind, json_decode($barco->coordenate_user, true))) {
//             $barco->user_barcos -= 1;
//             $barco->coordenate_user = json_encode(array_values(array_diff(json_decode($barco->coordenate_user, true), [$coordenateToFind])));
//         }
//     } else {
//         $coordenateToFind = json_encode([$latitud, $longitud]);
//         if ($barco && in_array($coordenateToFind, json_decode($barco->coordenate_rival, true))) {
//             $barco->rival_barcos -= 1;
//             $barco->coordenate_rival = json_encode(array_values(array_diff(json_decode($barco->coordenate_rival, true), [$coordenateToFind])));
//         }
//     }

//     $barco->save();

//     return response()->json(['message' => 'Bombardeo realizado con éxito']);
// }



// public function turnos(Request $request)
// {
//     $coordenates = $request->json()->all(); 
//       $user = auth()->user();
//     $barco = Barco::where($user)->latest()->first(); 

//     $coordenatesJson = json_encode($coordenates);

//     if ($barco && $coordenatesJson === $barco->coordenate_user) {
//         $coordenatesArray = json_decode($coordenatesJson, true);
        
//         foreach ($coordenatesArray as $coordenate) {
//             $coordenateToFind = json_encode($coordenate);
//             if (in_array($coordenateToFind, $barco->user_barcos)) {
//                 $barco->user_barcos = array_values(array_diff($barco->user_barcos, [$coordenateToFind])); 
//                 $barco->save();
//                 break; 
//             }
//         }
//         $turno = true; 
//     } else {
//         $turno = false; 
//     }

//     return response()->json(['turno' => $turno]);
// }




}