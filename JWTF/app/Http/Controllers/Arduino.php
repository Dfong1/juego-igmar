<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Arduino extends Controller
{
    public function sendToArduino($value)
{
    $arduinoUrl = 'http://arduino-ip-address/endpoint'; 

  
    $response = Http::post($arduinoUrl, [
        'value' => $value
    ]);

    
    if ($response->successful()) {
        return response()->json(['message' => 'Valor enviado correctamente a Arduino'], 200);
    } else {
        return response()->json(['message' => 'Error al enviar el valor a Arduino'], 500);
    }
}
}
