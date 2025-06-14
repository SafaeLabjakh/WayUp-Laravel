<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // POST /register
    public function register(Request $request)
    {
        // Validation simple directement ici
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Création utilisateur
        $user = User::create([
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json('User created successfully', 201);
    }

    // POST /login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        // Générer token (avec Sanctum par exemple)
        $token = $user->createToken('auth_token')->plainTextToken;

        // UserDTO simplifié
        $userDTO = [
            'id' => $user->id,
            'email' => $user->email,
            'firstName' => $user->first_name,
            'lastName' => $user->last_name,
            'metierSugg' => $user->metier_sugg,
            'testDone' => (bool) $user->test_done,
        ];

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $userDTO,
        ]);
    }
}
