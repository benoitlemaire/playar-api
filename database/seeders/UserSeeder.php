<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $superadmin = Role::create([
            'name' => 'superadmin',
        ]);

        $company = Role::create([
            'name' => 'company',
        ]);

        $freelance = Role::create([
            'name' => 'freelance',
        ]);

        $userSuperadmin = User::create([
            'email' => 'benoit.lemaire78@gmail.com',
            'password' => bcrypt('testtest'),
            'name' => 'Benoit Lemaire'
        ]);

        for ($i = 0; $i < 10; $i++) {
            $userCompany = User::create([
                'email' => $faker->email(),
                'password' => bcrypt('testtest'),
                'name' => $faker->name(),
            ]);
            $userCompany->attachRole($company);

            $userFreelance = User::create([
                'email' => $faker->email(),
                'password' => bcrypt('testtest'),
                'name' => $faker->name(),
                'phone' => $faker->phoneNumber(),
                'document_freelance' => $faker->imageUrl(),
                'filter_video' => $faker->imageUrl(),
                'instagram_account' => '@' . $faker->firstNameMale(),
            ]);
            $userFreelance->attachRole($freelance);
        }

        $userSuperadmin->attachRole($superadmin);
    }
}
