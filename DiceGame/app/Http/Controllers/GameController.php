<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Message;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    function createGame($user){
        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
        $winner = false;
        if($dice1 + $dice2 == 7){
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
        'dice1' => 'First dice ' . $dice1,
        'dice2' => 'Second dice ' . $dice2,
        'total' => ' ' . $total,
        'result' => $isWinner ? 'Congratulations, you won!' : 'Sorry, you lost!',
        'message' => 'Game registered!',
        'game' => $game,
    ]);
}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::all();

    return response()->json([
        'message' => 'All games retrieved successfully',
        'games' => $games,
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

        return response()->json(['message'=> 'All games deleted successfully!']);
    }
}
