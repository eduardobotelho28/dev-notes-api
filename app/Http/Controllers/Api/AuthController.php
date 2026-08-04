<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $password = $request->input('password');

        if (!is_string($password) || $password === '') {
            return response()->json([
                'message' => 'A senha é obrigatória.',
            ], 422);
        }

        if (!Hash::check($password, config('services.admin.password_hash'))) {
            return response()->json([
                'message' => 'Senha inválida.',
            ], 401);
        }

        $token = Str::random(64);

        AdminToken::create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays(5),
        ]);

        return response()->json([
            'token' => $token,
        ]);
    }
}
