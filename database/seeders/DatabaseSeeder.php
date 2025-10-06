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

        // Step 1: Create roles first
        $this->command->info('🔐 Creating roles...');
        $this->call(RoleSeeder::class);
        $this->command->info('✅ Roles created successfully');

        // Step 2: Create users
        $this->command->info('👤 Creating users...');
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $contentManager = User::factory()->create([
            'name' => 'Content Manager',
            'email' => 'content@example.com',
        ]);

        $otherUsers = User::factory(4)->create();
        $this->command->info('✅ Users created successfully');

        // Step 3: Assign roles to users
        $this->command->info('👥 Assigning roles to users...');
        $admin->assignRole('super-admin');
        $contentManager->assignRole('content-manager');

        // Assign content-manager role to 2 other users
        $otherUsers->take(2)->each(function ($user) {
            $user->assignRole('content-manager');
        });

        $this->command->info('✅ Roles assigned successfully');

        // Step 4: Seed categories
        $this->command->info('📂 Seeding categories...');
        $this->call(CategorySeeder::class);
        $this->command->info('✅ Categories seeded successfully');

        // Step 5: Seed tags
        $this->command->info('🏷️ Seeding tags...');
        $this->call(TagSeeder::class);
        $this->command->info('✅ Tags seeded successfully');

        // Step 6: Seed posts with relationships
        $this->command->info('📝 Seeding posts with categories and tags...');
        $this->call(PostSeeder::class);
        $this->command->info('✅ Posts seeded successfully');

        // Step 7: Seed menus
        $this->command->info('🍽️ Seeding menus...');
        $this->call(MenuSeeder::class);
        $this->command->info('✅ Menus seeded successfully');

        // Step 8: Seed settings
        $this->command->info('⚙️ Seeding settings...');
        $this->call(SettingsSeeder::class);
        $this->command->info('✅ Settings seeded successfully');

        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   Users: '.User::count());
        $this->command->info('   Categories: '.\App\Models\Category::count());
        $this->command->info('   Tags: '.\App\Models\Tag::count());
        $this->command->info('   Posts: '.\App\Models\Post::count());
        $this->command->info('   Menus: '.\App\Models\Menu::count());
        $this->command->info('   Menu Items: '.\App\Models\MenuItem::count());
        $this->command->info('   Settings: '.\App\Models\Setting::count());
    }
}
