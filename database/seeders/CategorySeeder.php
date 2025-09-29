<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'description' => 'Latest technology trends, software development, and digital innovations',
                'color' => '#007bff',
                'is_active' => true,
            ],
            [
                'name' => 'Web Development',
                'description' => 'Frontend, backend, and full-stack web development tutorials and insights',
                'color' => '#28a745',
                'is_active' => true,
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'iOS, Android, and cross-platform mobile app development',
                'color' => '#17a2b8',
                'is_active' => true,
            ],
            [
                'name' => 'Business',
                'description' => 'Business strategies, entrepreneurship, and industry insights',
                'color' => '#ffc107',
                'is_active' => true,
            ],
            [
                'name' => 'Design',
                'description' => 'UI/UX design, graphic design, and creative inspiration',
                'color' => '#e83e8c',
                'is_active' => true,
            ],
            [
                'name' => 'Lifestyle',
                'description' => 'Personal development, productivity, and work-life balance',
                'color' => '#6f42c1',
                'is_active' => true,
            ],
            [
                'name' => 'News',
                'description' => 'Industry news, announcements, and current events',
                'color' => '#fd7e14',
                'is_active' => true,
            ],
            [
                'name' => 'Archive',
                'description' => 'Older posts and historical content',
                'color' => '#6c757d',
                'is_active' => false,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
