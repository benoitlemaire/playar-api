<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ApplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < count(Offer::all()); $i++) {
            User::all()->random()->applies()->attach(Offer::all()->random()->id);
        }
    }
}
