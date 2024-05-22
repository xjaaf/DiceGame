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
    // Validar los datos de entrada con las reglas básicas
    $validatedData = $request->validate([
        'name' => 'nullable|string',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:5',
        'nickname' => 'nullable', // Permitir que el campo nickname sea nulo o una cadena
    ]);

    // Asignar 'anonymous' como nickname si no se proporciona uno
    $nickname = $request->filled('nickname') ? $request->nickname : 'anonymous';

    // Verificar si el nickname ya existe en la base de datos
    $existingNickname = User::where('nickname', $nickname)->exists();

    // Si el nickname ya existe y no es 'anonymous', devolver un error
    if ($existingNickname && $nickname !== 'anonymous') {
        return response()->json(['error' => 'Nickname already exists'], 422);
    }

    // Crear el usuario con los datos validados
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
        // Validar los datos de entrada con las reglas básicas
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        // Buscar el usuario por su email
        $user = User::where('email', $validatedData['email'])->first();

        // Verificar si el usuario existe y si la contraseña es correcta
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
        // Revocar el token de acceso del usuario autenticado
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


    public function destroy(string $id)
    {
        // Método vacío por ahora
    }
}
