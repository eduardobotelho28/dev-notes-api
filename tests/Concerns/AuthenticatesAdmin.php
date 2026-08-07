<?php

namespace Tests\Concerns;

use App\Models\AdminToken;
use Illuminate\Support\Str;

trait AuthenticatesAdmin
{
    protected function adminToken(): string
    {
        $token = Str::random(64);

        AdminToken::create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDay(),
        ]);

        return $token;
    }

    protected function withAdminAuth(): array
    {
        return ['Authorization' => 'Bearer ' . $this->adminToken()];
    }
}