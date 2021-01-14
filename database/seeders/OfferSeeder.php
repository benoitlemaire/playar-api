<?php

namespace Database\Seeders;

use App\Models\Offer;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $offers_count = rand(10, 35);

        for ($i = 0; $i < $offers_count; $i++) {
            Offer::create([
                'title' => $faker->sentence(10, true),
                'description' => $faker->text(2500),
                'company_logo' => $faker->imageUrl(200,200),
                'user_id' => $faker->numberBetween(0, 21),
            ]);

        }
    }
}
