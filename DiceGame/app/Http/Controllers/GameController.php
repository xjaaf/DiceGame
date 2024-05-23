<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Message;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    function createGame($user)
    {
        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
        $winner = false;
        if ($dice1 + $dice2 == 7) {
            $winner = true;
        }

        return [
            'dice1' => $dice1,
            'dice2' => $dice2,
            'winner' => $winner,
            'user_id' => $user->id
        ];
    }

    public function play()
{
    // Autenticar al usuario
    $user = Auth::guard('api')->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Lanzar los dados
    $dice1 = rand(1, 6);
    $dice2 = rand(1, 6);
    $total = $dice1 + $dice2;

    // Determinar si es ganador
    $isWinner = ($total == 7);

    // Guardar el resultado en la base de datos
    $game = Game::create([
        'dice1' => $dice1,
        'dice2' => $dice2,
        'winner' => $isWinner,
        'user_id' => $user->id
    ]);

    // Devolver la respuesta JSON
    return response()->json([
        'welcome' => 'Welcome to the Dice Game!',
        'dice1' => 'Your first dice is ' . $dice1,
        'dice2' => 'Your second dice is ' . $dice2,
        'total' => 'The total of your dice is ' . $total,
        'result' => $isWinner ? 'Congratulations, you won!' : 'Sorry, you lost!',
        'status' => 'success',
        'message' => 'Your game has been registered successfully!',
        'game' => $game,
    ]);
}


    /**
     * Display a listing of the resource.
     */

    public function getAllGames($id)
    {
        $userGames = Game::where('user_id', $id)->get();

        if ($userGames->isEmpty()) {
            return response()->json([
                'message' => 'You haven\'t made any plays yet',
                'number_of_games' => 0,
                'number_of_wins' => 0, 
                'games' => [],
            ]);
        }

        $gameCount = $userGames->count();

        // Calcular el número de partidas ganadas
        $numberOfWins = $userGames->where('isWon', true)->count();

        return response()->json([
            'message' => 'All games retrieved successfully',
            'number_of_games' => $gameCount,
            'number_of_wins' => $numberOfWins, // Nuevo: Incluir el número de partidas ganadas
            'games' => $userGames,
        ]);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Game::where('user_id', $id)->delete();

        return response()->json(['message' => 'All games deleted successfully!']);
    }
}
