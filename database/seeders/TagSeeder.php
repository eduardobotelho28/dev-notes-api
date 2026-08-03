<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Redes',
            'Segurança',
            'Algoritmos',
            'Lógica',
            'Estrutura de Dados',
            'Sistemas Operacionais',
            'Performance',
            'Inteligência Artificial',
            'Autenticação',
            'Arquitetura',
            'System Design',
            'Banco de Dados',
            'UX/UI',
        ];

        foreach ($tags as $name) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}