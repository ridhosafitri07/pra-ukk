<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // POST /api/register
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'end_user', // otomatis end_user
        ]);

        return response()->json([
            'message' => 'Registered',
            'user'    => $user,
        ], 201);
    }

    // POST /api/login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // token untuk mobile
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => 'Logged in',
            'token'   => $token,
            'token_type' => 'Bearer',
            'user'    => $user,
        ]);
    }

    // POST /api/logout
    public function logout(Request $request)
    {
        // hapus token yang dipakai saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    // GET /api/user (profil saya)
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
