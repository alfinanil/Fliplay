<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $media = Media::factory(12)->create();
        $pengguna = Pengguna::factory(5)->create();

        // beri tiap pengguna 2-4 media acak di koleksinya
        $pengguna->each(function (Pengguna $p) use ($media) {
            $p->media()->attach($media->random(rand(2, 4))->pluck('id')->all());
        });
    }
}
