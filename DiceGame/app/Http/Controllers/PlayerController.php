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
        $loser = User::withCount(['games as games_played', 'games as games_won' => function ($query) {
                $query->where('winner', true);
            }])
            ->get()
            ->sortBy('average_success_rate')
            ->first();

        return $loser 
            ? response()->json(['Worst Success Rate' => $loser], 200)
            : response()->json(['message' => 'No players found'], 404);
    }

    public function destroy(string $id)
    {
        // Método vacío por ahora
    }
}
