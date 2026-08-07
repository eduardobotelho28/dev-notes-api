<?php

namespace Tests\Feature;

use App\Models\Concept;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\AuthenticatesAdmin;
use Tests\TestCase;

class ConceptTest extends TestCase
{
    use RefreshDatabase, AuthenticatesAdmin;

    public function test_lista_conceitos_paginados(): void
    {
        Concept::factory()->count(20)->create();

        $response = $this->getJson('/api/concepts?per_page=10');

        $response->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function test_busca_por_titulo(): void
    {
        Concept::factory()->create(['title' => 'Load Balancer']);
        Concept::factory()->create(['title' => 'Rate Limiting']);

        $response = $this->getJson('/api/concepts?search=load');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Load Balancer');
    }

    public function test_filtra_por_tag(): void
    {
        $tagRedes = Tag::factory()->create(['name' => 'Redes', 'slug' => 'redes']);
        $tagSeguranca = Tag::factory()->create(['name' => 'Segurança', 'slug' => 'seguranca']);

        $concept1 = Concept::factory()->create();
        $concept1->tags()->attach($tagRedes);

        $concept2 = Concept::factory()->create();
        $concept2->tags()->attach($tagSeguranca);

        $response = $this->getJson('/api/concepts?tag=redes');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $concept1->id);
    }

    public function test_mostra_concept_por_slug(): void
    {
        $concept = Concept::factory()->create(['slug' => 'load-balancer']);

        $response = $this->getJson('/api/concepts/load-balancer');

        $response->assertOk()
            ->assertJsonPath('id', $concept->id);
    }

    public function test_nao_permite_criar_concept_sem_token(): void
    {
        $response = $this->postJson('/api/concepts', [
            'title' => 'Load Balancer',
            'tldr' => 'Distribui carga entre servidores',
            'summary' => '## O que é...',
        ]);

        $response->assertStatus(401);
    }

    public function test_cria_concept_com_token_valido(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->postJson('/api/concepts', [
            'title' => 'Load Balancer',
            'tldr' => 'Distribui carga entre servidores',
            'summary' => '## O que é...',
            'tags' => [$tag->id],
            'links' => [
                ['title' => 'Docs', 'url' => 'https://example.com', 'type' => 'documentacao'],
            ],
        ], $this->withAdminAuth());

        $response->assertCreated()
            ->assertJsonPath('title', 'Load Balancer')
            ->assertJsonPath('slug', 'load-balancer')
            ->assertJsonCount(1, 'tags')
            ->assertJsonCount(1, 'links');

        $this->assertDatabaseHas('concepts', ['title' => 'Load Balancer']);
    }

    public function test_gera_slug_unico_em_titulos_duplicados(): void
    {
        Concept::factory()->create(['title' => 'Load Balancer', 'slug' => 'load-balancer']);

        $response = $this->postJson('/api/concepts', [
            'title' => 'Load Balancer',
            'tldr' => 'Outro resumo',
            'summary' => '## O que é...',
        ], $this->withAdminAuth());

        $response->assertCreated()
            ->assertJsonPath('slug', 'load-balancer-1');
    }

    public function test_valida_campos_obrigatorios_ao_criar(): void
    {
        $response = $this->postJson('/api/concepts', [], $this->withAdminAuth());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'tldr', 'summary']);
    }

    public function test_atualiza_concept_parcialmente(): void
    {
        $concept = Concept::factory()->create(['field_notes' => null]);

        $response = $this->putJson("/api/concepts/{$concept->id}", [
            'field_notes' => 'Usei isso em produção e...',
        ], $this->withAdminAuth());

        $response->assertOk()
            ->assertJsonPath('field_notes', 'Usei isso em produção e...')
            ->assertJsonPath('title', $concept->title); // não deve ter mudado
    }

    public function test_nao_permite_atualizar_sem_token(): void
    {
        $concept = Concept::factory()->create();

        $response = $this->putJson("/api/concepts/{$concept->id}", [
            'tldr' => 'Novo tldr',
        ]);

        $response->assertStatus(401);
    }

    public function test_deleta_concept_com_token(): void
    {
        $concept = Concept::factory()->create();

        $response = $this->deleteJson("/api/concepts/{$concept->id}", [], $this->withAdminAuth());

        $response->assertStatus(204);
        $this->assertSoftDeleted('concepts', ['id' => $concept->id]);
    }

    public function test_nao_permite_deletar_sem_token(): void
    {
        $concept = Concept::factory()->create();

        $response = $this->deleteJson("/api/concepts/{$concept->id}");

        $response->assertStatus(401);
        $this->assertDatabaseHas('concepts', ['id' => $concept->id, 'deleted_at' => null]);
    }
}