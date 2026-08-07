<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.admin.password_hash' => Hash::make('senha-correta')]);
    }

    public function test_login_com_senha_correta_retorna_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'senha-correta',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token']);

        $this->assertDatabaseCount('admin_tokens', 1);
    }

    public function test_login_com_senha_incorreta_retorna_401(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'senha-errada',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseCount('admin_tokens', 0);
    }

    public function test_login_bloqueia_apos_5_tentativas(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/auth/login', ['password' => 'senha-errada']);
        }

        $response = $this->postJson('/api/auth/login', ['password' => 'senha-errada']);

        $response->assertStatus(429);
    }
}