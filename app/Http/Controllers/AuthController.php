<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attributes = $request->validate([
           'name'       => ['required', 'string', 'max:255'],
           'email'      => ['required', 'string', 'max:255', 'email', 'unique:users'],
           'password'   => ['required', 'string', 'max:255', 'confirmed'],
        ]);

        $attributes['password'] = Hash::make($attributes['password']);

        $user = User::create($attributes);

        return $user;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'     => ['required', 'string', 'max:255', 'email'],
            'password'  => ['required', 'string', 'max:255'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }
        
        $user->tokens()->where('name', 'access-token')->delete();

        $token = $user->createToken('access-token');

        return response()->json([
            'accessToken' => $token->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    }
}
