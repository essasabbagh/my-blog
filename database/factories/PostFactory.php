<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->unique()->sentence;
        $slug = Str::slug($title);

        return [
            'title' => $title,
            'slug' => $slug,
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->optional()->sentence,
            'status' => $this->faker->randomElement(['draft', 'published', 'archived']),
            'published_at' => $this->faker->optional()->dateTime,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
