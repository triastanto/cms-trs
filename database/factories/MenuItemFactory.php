<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_id' => \App\Models\Menu::factory(),
            'parent_id' => null, // Will be set in specific states
            'title' => $this->faker->words(2, true),
            'url' => $this->faker->slug(),
            'target' => '_self',
            'icon' => $this->faker->optional(0.3)->randomElement(['home', 'user', 'settings', 'info', 'mail']),
            'css_class' => $this->faker->optional(0.2)->randomElement(['btn-primary', 'btn-secondary', 'text-bold']),
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
            'is_external' => false,
        ];
    }

    /**
     * Create a root menu item (no parent).
     */
    public function root(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
        ]);
    }

    /**
     * Create a child menu item.
     */
    public function child(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Create an external menu item.
     */
    public function external(): static
    {
        return $this->state(fn (array $attributes) => [
            'url' => $this->faker->url(),
            'is_external' => true,
            'target' => '_blank',
        ]);
    }

    /**
     * Create an inactive menu item.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
