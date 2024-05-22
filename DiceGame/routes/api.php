<?php 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\GameController;
use App\Http\Middleware\EnsureUserID;   


Route::post('/player', [PlayerController::class, 'register']);
Route::post('/player/login', [PlayerController::class, 'login']);
Route::post('/player/logout', [PlayerController::class, 'logout']);
Route::get('/players/ranking/loser', [PlayerController::class, 'loserRanking']);
Route::get('/players/ranking/winner', [PlayerController::class, 'winnerRanking']);


// Rutas protegidas por autenticación
//Route::middleware('auth:api')->group(function () {
    //Route::post('/player/logout', [PlayerController::class, 'logout']);
    //Route::put('/player/update/{id}', [PlayerController::class, 'update']);
    //Route::get('/player/index', [PlayerController::class, 'index']);
    // });

Route::middleware('auth:api', 'ensureUserID', 'role:player')->group(function () {
    Route::put('/player/{id}', [PlayerController::class, 'update']);
    Route::post('/player/{id}/games/', [GameController::class, 'play']);
    Route::delete('/player/{id}/games', [GameController::class, 'destroy']);
    Route::get('/player/{id}/games', [GameController::class, 'getAllGames']);
});

Route::middleware('auth:api', 'role:admin')->group(function () {
    
    Route::get('/players', [PlayerController::class, 'averageSuccessRate']);
    //Route::get('/players', [PlayerController::class, 'index']);

});

//Route::post('/game/play', [GameController::class, 'play']);
//Route::get('/game/index', [GameController::class, 'index']);

//Route::middleware('auth:api', 'role:player')->group(function () {
    //Route::post('/game/play', [GameController::class, 'play']);
    //Route::get('/game/index', [GameController::class, 'index']);
    //});


