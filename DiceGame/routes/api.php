<?php 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;

Route::post('/player/register', [PlayerController::class, 'register']);
Route::post('/player/login', [PlayerController::class, 'login']);

// Rutas protegidas por autenticación
Route::middleware('auth:api')->group(function () {
    Route::post('/player/logout', [PlayerController::class, 'logout']);
    Route::put('/player/update/{id}', [PlayerController::class, 'update']);
    Route::get('/player/index', [PlayerController::class, 'index']);
});
