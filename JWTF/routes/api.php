<?php


use App\Http\Controllers\BarcosController;
use App\Http\Controllers\JuegosController;
use App\Mail\ValidatorEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\DispositivosController;
use App\Http\Controllers\ReparacionesController;
use App\Http\Controllers\ReparacionDispositivoController;
use App\Http\Controllers\AccesoriosController;
use App\Http\Controllers\OrdenVentaController;
use App\Http\Controllers\OrdenVentaAccesorioController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\IngresoReparacionesController;
use App\Http\Controllers\LogHistoryController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\BuscarRivalesController;
use App\Http\Controllers\EstadisticasController;



Route::group([

    'prefix' => 'auth'

], function ($router) {
    Route::post('register', 'App\Http\Controllers\AuthController@register');
    Route::post('login', [AuthController::class,'login'])->middleware('activate2');
    Route::post('verificarlogin',[AuthController::class,'verificarlogin'])->middleware('activate2');

    Route::post('verificar', [AuthController::class,'verifyTwoFactorCode'])->middleware(['active']);
});


Route::get('activate/{user}', 'App\Http\Controllers\AuthController@activate')->name('activate')->middleware('signed');


Route::group([
    'middleware' => ['api', 'active', 'twoFactor'],
    'prefix' => 'user'
], function ($router) {

    Route::get('get',[UserController::class,'index']);
    Route::post('post',[UserController::class,'store'])->middleware('authrole2');
    Route::delete('delete/{id}',[UserController::class,'destroy'])->middleware('authrole')->where('id','[0-9]+');
    Route::put('put/{id}',[UserController::class,'update'])->middleware('authrole2')->where('id','[0-9]+');
    
    
    Route::get('logs/{id}',[LogHistoryController::class,'index']);
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    
    Route::post('/postear',[BuscarRivalesController::class,'post']);
    Route::post('/updatear',[BuscarRivalesController::class,'update']);
    Route::get('me', 'App\Http\Controllers\AuthController@me');
    
    Route::get('registrobatalla',[EstadisticasController::class,'registrobatallas']);
    Route::get('movimiento/{id}',[JuegosController::class,'hacerMovimiento']);
    Route::post('/buscar/partida', [BuscarRivalesController:: class, 'joinQueue']);
    Route::post('/cancelar/partida', [BuscarRivalesController:: class, 'joinQueue']);
    Route::get('/get-queue', [BuscarRivalesController:: class, 'getQueue']);

    Route::group(['prefix' => 'juego'], function () {
        // Endpoint para colocar los barcos
        Route::post('{gameId}/colocar-barcos', [BarcosController::class,'colocarBarcos']);

        // Endpoint para realizar un movimiento
        Route::post('{gameId}/hacer-movimiento', [JuegosController::class, 'hacerMovimiento']);
        Route::post('get-game', [JuegosController::class, 'obtenerJuegoActual']);

        // Otros endpoints del juego...
        Route::get('getBarcosCount',[BarcosController::class,'getBarcosCount'])->middleware('auth:api');
    });
});


Route::post('coordinates',[JuegosController::class,'storecoordenates']);
Route::post('turnos',[JuegosController::class,'turnos']);


Route::post('guardarestadisticas',[EstadisticasController::class,'store']);
