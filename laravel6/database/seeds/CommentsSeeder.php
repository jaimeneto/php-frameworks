<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Comment;
use App\Post;
use App\User;

class CommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $posts = Post::all()->pluck('id')->toArray();
        $users = User::all()->pluck('id')->toArray();
        foreach (range(1, 3000) as $index) {
            $comment = new Comment();
            $comment->text = $faker->paragraph;
            $comment->post_id = $faker->randomElement($posts);
            $comment->user_id = $faker->randomElement($users);
            // Aleatoriamente aprova ou não o comentário
            if ((bool) rand(0, 1)) {
                $comment->approved_at = now();
            }
            $comment->save();
        }
    }
}
