<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required'      => 'Le nom complet est obligatoire.',
            'email.required'     => "L'adresse e-mail est obligatoire.",
            'email.email'        => "L'adresse e-mail n'est pas valide.",
            'email.unique'       => 'Cette adresse e-mail est déjà utilisée.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les deux mots de passe ne sont pas identiques.',
        ]);

        // Le mot de passe est haché automatiquement par le cast 'hashed' du modèle User.
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        return response()->json([
            'user'  => $user->only('id', 'name', 'email'),
            'token' => $user->createToken('spa')->plainTextToken,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Même message si l'e-mail n'existe pas ou si le mot de passe est faux :
        // on ne révèle pas quels comptes existent.
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Adresse e-mail ou mot de passe incorrect.'],
            ]);
        }

        return response()->json([
            'user'  => $user->only('id', 'name', 'email'),
            'token' => $user->createToken('spa')->plainTextToken,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->only('id', 'name', 'email'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }
}