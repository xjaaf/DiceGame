<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 
use Illuminate\Support\Facades\Validator;
use phpseclib3\Crypt\EC\Curves\nistb233;

class PlayerController extends Controller
{
    public function register(Request $request)
{
    $validatedData = $request->validate([
        'name' => 'nullable|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:5',
        'nickname' => 'nullable',
    ]);

    
    $nickname = $request->filled('nickname') ? $request->nickname : 'anonymous';

    
    $existingNickname = User::where('nickname', $nickname)->exists();

    if ($existingNickname && $nickname !== 'anonymous') {
        return response()->json(['error' => 'Nickname already exists'], 422);
    }

    $user = User::create([
        'name' => $validatedData['name'],
        'nickname' => $nickname,
        'email' => $validatedData['email'],
        'password' => Hash::make($validatedData['password']),
    ]);
    $user->assignRole('player');
    $token = $user->createToken('MyApp')->accessToken;
    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user,
        'access_token' => $token,
    ], 201);
}

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('MyApp')->accessToken;
        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }

    public function index()
    {
        $users = User::all();
        return response()->json([
            'message' => 'All users retrieved successfully',
            'users' => $users,
        ]);
    }

    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'nickname' => 'required|string|unique:users,nickname,' . $id,
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $user = User::findOrFail($id);

    $newNickname = $request->input('nickname');
    $user->nickname = $newNickname;
    $user->save();

    return response()->json(['message' => 'Player nickname updated successfully'], 200);
}

public function loserRanking()
{
    $user = Auth::guard('api')->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $loser = User::withCount(['games as games_played', 'games as games_won' => function ($query) {
            $query->where('winner', true);
        }])
        ->get()
        ->sortBy('average_success_rate')
        ->first();

    return $loser 
        ? response()->json(['Player with worst success rate' => $loser], 200)
        : response()->json(['message' => 'No players found'], 404);
}

    public function winnerRanking()
{
    $user = Auth::guard('api')->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $winner = User::withCount(['games as games_won' => function ($query) {
            $query->where('winner', true);
        }])
        ->orderByDesc('games_won')
        ->first();

    return $winner 
        ? response()->json(['Player with most games won' => $winner], 200)
        : response()->json(['message' => 'No players found'], 404);
}


public function ranking()
{

    $user = Auth::guard('api')->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $players = User::withCount(['games as games_played'])
        ->withCount(['games as games_won' => function ($query) {
            $query->where('winner', true);
        }])
        ->get()
        ->each(function ($player) {
            $player->average_success_rate = ($player->games_played > 0)
                ? round(($player->games_won / $player->games_played) * 100, 1)
                : 0;
        });

    $total_players = $players->count();
    $total_average_success_rate = $players->avg('average_success_rate');

    return response()->json([
        'message' => 'Average success rate calculated successfully',
        'total_players' => $total_players,
        'total_average_success_rate' => $total_average_success_rate,
        'players' => $players,
    ]);
}
public function averageSuccessRate()
{
    $user = Auth::guard('api')->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $players = User::whereHas('roles', function ($query) {
            $query->where('name', 'player');
        })
        ->withCount(['games as games_played'])
        ->withCount(['games as games_won' => function ($query) {
            $query->where('winner', true);
        }])
        ->get()
        ->each(function ($player) {
            $player->average_success_rate = ($player->games_played > 0)
                ? round(($player->games_won / $player->games_played) * 100, 1)
                : 0;
        });

    $total_players = $players->count();
    $total_average_success_rate = $players->avg('average_success_rate');

    // Obtener el porcentaje medio de cada jugador
    $players->transform(function ($player) use ($total_average_success_rate) {
        $player->player_average_success_rate = $total_average_success_rate;
        return $player;
    });

    return response()->json([
        'message' => 'Average success rate calculated successfully',
        'total_players' => $total_players,
        'total_average_success_rate' => $total_average_success_rate,
        'players' => $players,
    ]);
}

    public function destroy(string $id)
    {
        // Método vacío por ahora
    }
}
