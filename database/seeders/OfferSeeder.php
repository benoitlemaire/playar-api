<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\User;
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
        $offers_count = rand(50, 100);

        for ($i = 0; $i < $offers_count; $i++) {
            $random_user = User::all()->random();
            Offer::create([
                'title' => $faker->sentence(10, true),
                'author' =>  $random_user->name,
                'description' => $faker->text(2500),
                'company_logo' => $faker->imageUrl(200,200),
                'user_id' => $random_user->id,
            ]);

        }
    }
}
