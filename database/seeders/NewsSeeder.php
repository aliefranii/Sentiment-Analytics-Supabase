<?php

// database/seeders/NewsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use Faker\Factory as Faker;

class NewsSeeder extends Seeder
{
    public function run()
    {
        // Membuat instance Faker untuk data dummy
        $faker = Faker::create();

        // Menambahkan 50 data dummy ke tabel news
        for ($i = 0; $i < 50; $i++) {
            News::create([
                'url' => $faker->url,
                'desc' => $faker->paragraph,
                'title' => $faker->sentence,
                'date' => $faker->date,
                'source' => $faker->company,
                'category' => $faker->word,
                'sentiment' => $faker->randomElement(['positive', 'negative', 'neutral']),
                'alasan' => $faker->sentence,
            ]);
        }
    }
}

