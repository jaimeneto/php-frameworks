<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $roles = ['user', 'admin'];
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user->name = $faker->name();
            $user->email = $faker->email();
            $user->role = $faker->randomElement($roles);
            $user->password = bcrypt('laravel');
            $user->save();
            if ((bool) rand(0, 1)) { // Aleatoriamente ativa ou não o usuário
                $user->markEmailAsVerified();
            }
        }
    }
}
