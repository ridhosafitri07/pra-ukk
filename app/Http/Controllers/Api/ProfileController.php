<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // PUT /api/user  (update nama/email)
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => ['sometimes','required','string','max:100'],
            'email' => ['sometimes','required','email','max:150', Rule::unique('users','email')->ignore($user->id)],
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated',
            'user' => $user,
        ]);
    }

    // PUT /api/user/password  (ganti password)
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required'],
            'new_password'     => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);

        // opsional: revoke token lama & berikan token baru
        $user->tokens()->delete();
        $newToken = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => 'Password changed',
            'token'   => $newToken,
            'token_type' => 'Bearer',
        ]);
    }
}
