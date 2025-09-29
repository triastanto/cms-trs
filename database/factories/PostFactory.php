<?php

namespace Database\Factories;

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
    public function definition(): array
    {
        $title = fake()->sentence(rand(3, 8));

        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'content' => fake()->paragraphs(rand(5, 15), true),
            'excerpt' => fake()->sentence(rand(10, 20)),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'featured_image' => fake()->optional(0.7)->imageUrl(800, 600, 'nature'),
            'meta_title' => fake()->optional(0.8)->sentence(rand(5, 10)),
            'meta_description' => fake()->optional(0.8)->sentence(rand(15, 25)),
            'published_at' => fake()->optional(0.6)->dateTimeBetween('-1 year', 'now'),
            'user_id' => \App\Models\User::factory(),
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the post is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    /**
     * Indicate that the post is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
            'published_at' => fake()->dateTimeBetween('-1 year', '-1 month'),
        ]);
    }
}
