<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Post;
use App\User;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $users = User::where('role', 'admin')->get()
            ->pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            $post = new Post();
            $post->title = $faker->sentence();
            $post->text = $faker->realText(500);
            $post->user_id = $faker->randomElement($users);
            $post->save();
        }
    }
}
