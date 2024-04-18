<?php

namespace App\Http\Controllers;

use App\Events\ActualizaJuego;
use App\Events\CambiarTurno;
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
        // Validar la solicitud
        $this->validate($request, [
            'horizontal' => 'required|integer',
            'vertical' => 'required|integer',
        ]);

        // Obtener el juego
        $game = Game::findOrFail($gameId);

        // Verificar si es el turno del usuario actual
        $currentUser = Auth::user();
        if ($game->active_player_id !== $currentUser->id) {
            return response()->json(['error' => 'No es tu turno >:('], 403);
        }

        // Obtener las coordenadas del movimiento desde la solicitud
        $x = $request->horizontal;
        $y = $request->vertical;

        // Verificar si las coordenadas están dentro del rango del tablero
        if ($x < 0 || $x >= count($game->board) || $y < 0 || $y >= count($game->board[0])) {
            return response()->json(['error' => 'Coordenadas no validas'], 400);
        }

        // Realizar el ataque y actualizar el estado del tablero
        // Supongamos que aquí implementas la lógica para determinar si el ataque fue exitoso o no
        $isSuccessful = $game->ship_positions()->where('x', $x)->where('y', $y)->exists();
        $game->board[$x][$y] = $isSuccessful ? 1 : 2;

        // Cambiar el turno al otro jugador
        $game->active_player_id = $game->users->where('id', '!=', $currentUser->id)->first()->id;

        // Guardar los cambios en la base de datos
        $game->save();

        // Emitir evento de cambio de turno
        broadcast(new CambiarTurno($game));

        // Emitir evento de actualización del juego
        broadcast(new ActualizaJuego($game));

        return response()->json(['message' => 'Movimiento hecho con satisfacción :)']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



public function bombardear($id, $barcotumbado, $latitud, $longitud)
{
    $barco = Barco::find($id);

    if ($barcotumbado) {
        $coordenateToFind = json_encode([$latitud, $longitud]);
        if ($barco && in_array($coordenateToFind, json_decode($barco->coordenate_user, true))) {
            $barco->user_barcos -= 1;
            $barco->coordenate_user = json_encode(array_values(array_diff(json_decode($barco->coordenate_user, true), [$coordenateToFind])));
        }
    } else {
        $coordenateToFind = json_encode([$latitud, $longitud]);
        if ($barco && in_array($coordenateToFind, json_decode($barco->coordenate_rival, true))) {
            $barco->rival_barcos -= 1;
            $barco->coordenate_rival = json_encode(array_values(array_diff(json_decode($barco->coordenate_rival, true), [$coordenateToFind])));
        }
    }

    $barco->save();

    return response()->json(['message' => 'Bombardeo realizado con éxito']);
}



public function turnos(Request $request)
{
    $coordenates = $request->json()->all(); 
      $user = auth()->user();
    $barco = Barco::where($user)->latest()->first(); 

    $coordenatesJson = json_encode($coordenates);

    if ($barco && $coordenatesJson === $barco->coordenate_user) {
        $coordenatesArray = json_decode($coordenatesJson, true);
        
        foreach ($coordenatesArray as $coordenate) {
            $coordenateToFind = json_encode($coordenate);
            if (in_array($coordenateToFind, $barco->user_barcos)) {
                $barco->user_barcos = array_values(array_diff($barco->user_barcos, [$coordenateToFind])); 
                $barco->save();
                break; 
            }
        }
        $turno = true; 
    } else {
        $turno = false; 
    }

    return response()->json(['turno' => $turno]);
}




}
