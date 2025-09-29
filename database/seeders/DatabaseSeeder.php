<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Step 1: Create users first
        $this->command->info('👤 Creating users...');
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory(5)->create();
        $this->command->info('✅ Users created successfully');

        // Step 2: Seed categories
        $this->command->info('📂 Seeding categories...');
        $this->call(CategorySeeder::class);
        $this->command->info('✅ Categories seeded successfully');

        // Step 3: Seed tags
        $this->command->info('🏷️ Seeding tags...');
        $this->call(TagSeeder::class);
        $this->command->info('✅ Tags seeded successfully');

        // Step 4: Seed posts with relationships
        $this->command->info('📝 Seeding posts with categories and tags...');
        $this->call(PostSeeder::class);
        $this->command->info('✅ Posts seeded successfully');

        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   Users: ' . User::count());
        $this->command->info('   Categories: ' . \App\Models\Category::count());
        $this->command->info('   Tags: ' . \App\Models\Tag::count());
        $this->command->info('   Posts: ' . \App\Models\Post::count());
    }
}
