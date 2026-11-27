<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Media> */
class MediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'judul' => ucwords(fake()->unique()->words(3, true)),
            'jenis' => fake()->randomElement(Media::JENIS),
            'genre' => fake()->randomElement(['Aksi', 'Drama', 'Komedi', 'Horor', 'Fantasi', 'Petualangan', 'Romantis']),
            'tahun_rilis' => fake()->numberBetween(1995, 2026),
            'deskripsi' => fake()->sentence(),
        ];
    }
}
