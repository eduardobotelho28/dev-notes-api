<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\AuthenticatesAdmin;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase, AuthenticatesAdmin;

    public function test_faz_upload_de_imagem_valida(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('teste.jpg', 500, 'image/jpeg');

        $response = $this->postJson('/api/uploads/image', [
            'image' => $file,
        ], $this->withAdminAuth());

        $response->assertCreated()
            ->assertJsonStructure(['path', 'url']);

        Storage::disk('public')->assertExists('concepts/' . basename($response->json('path')));
    }

    public function test_rejeita_arquivo_maior_que_o_limite(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('grande.jpg')->size(3000); // 3MB > limite de 2MB

        $response = $this->postJson('/api/uploads/image', [
            'image' => $file,
        ], $this->withAdminAuth());

        $response->assertStatus(422);
    }

    public function test_rejeita_arquivo_que_nao_e_imagem(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('arquivo.pdf', 100, 'application/pdf');

        $response = $this->postJson('/api/uploads/image', [
            'image' => $file,
        ], $this->withAdminAuth());

        $response->assertStatus(422);
    }

    public function test_nao_permite_upload_sem_token(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('teste.jpg');

        $response = $this->postJson('/api/uploads/image', [
            'image' => $file,
        ]);

        $response->assertStatus(401);
    }
}