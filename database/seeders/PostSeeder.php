<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 posts
        Post::factory(10)->create()->each(function ($post) {
            // Attach random categories to each post
            $categories = Category::inRandomOrder()->take(rand(1, 3))->pluck('id');
            $post->categories()->attach($categories);

            // Attach random tags to each post
            $tags = Tag::inRandomOrder()->take(rand(1, 5))->pluck('id');
            $post->tags()->attach($tags);
        });
    }
}
