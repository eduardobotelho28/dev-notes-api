<?php

namespace App\Http\Middleware;

use App\Models\AdminToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token não fornecido'], 401);
        }

        $hashed = hash('sha256', $token);

        $valid = AdminToken::where('token_hash', $hashed)
            ->where('expires_at', '>', now())
            ->exists();

        if (!$valid) {
            return response()->json(['message' => 'Token inválido ou expirado'], 401);
        }

        return $next($request);
    }
}