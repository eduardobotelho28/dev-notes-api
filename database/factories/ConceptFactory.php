<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ConceptFactory extends Factory
{
    public function definition(): array
    {
        $title = ucfirst($this->faker->unique()->words(3, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'tldr' => $this->faker->sentence(),
            'summary' => "## O que é\n\n" . $this->faker->paragraph(),
            'field_notes' => $this->faker->optional()->paragraph(),
            'image_path' => null,
        ];
    }
}