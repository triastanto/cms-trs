<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create additional users
        User::factory(5)->create();

        // Create posts with different statuses
        Post::factory(10)->published()->create();
        Post::factory(5)->draft()->create();
        Post::factory(3)->archived()->create();
    }
}
