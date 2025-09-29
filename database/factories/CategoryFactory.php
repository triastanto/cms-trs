<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Test Category',
            'slug' => 'test-category-'.rand(1000, 9999),
            'description' => 'This is a test category description.',
            'color' => '#3B82F6',
            'parent_id' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the category has a parent.
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Category::factory(),
        ]);
    }
}
