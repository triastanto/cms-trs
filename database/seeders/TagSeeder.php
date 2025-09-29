<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            // Programming Languages
            [
                'name' => 'PHP',
                'description' => 'PHP programming language and frameworks',
                'color' => '#8892bf',
                'is_active' => true,
            ],
            [
                'name' => 'JavaScript',
                'description' => 'JavaScript development and frameworks',
                'color' => '#f7df1e',
                'is_active' => true,
            ],
            [
                'name' => 'Python',
                'description' => 'Python programming language',
                'color' => '#3776ab',
                'is_active' => true,
            ],
            [
                'name' => 'TypeScript',
                'description' => 'TypeScript development',
                'color' => '#007acc',
                'is_active' => true,
            ],

            // Frameworks
            [
                'name' => 'Laravel',
                'description' => 'Laravel PHP framework',
                'color' => '#ff2d20',
                'is_active' => true,
            ],
            [
                'name' => 'React',
                'description' => 'React JavaScript library',
                'color' => '#61dafb',
                'is_active' => true,
            ],
            [
                'name' => 'Vue.js',
                'description' => 'Vue.js JavaScript framework',
                'color' => '#4fc08d',
                'is_active' => true,
            ],
            [
                'name' => 'Next.js',
                'description' => 'Next.js React framework',
                'color' => '#000000',
                'is_active' => true,
            ],
            [
                'name' => 'Symfony',
                'description' => 'Symfony PHP framework',
                'color' => '#000000',
                'is_active' => true,
            ],

            // Content Types
            [
                'name' => 'Tutorial',
                'description' => 'Step-by-step tutorials and guides',
                'color' => '#17a2b8',
                'is_active' => true,
            ],
            [
                'name' => 'Tips',
                'description' => 'Quick tips and tricks',
                'color' => '#28a745',
                'is_active' => true,
            ],
            [
                'name' => 'Best Practices',
                'description' => 'Industry best practices and standards',
                'color' => '#ffc107',
                'is_active' => true,
            ],
            [
                'name' => 'Case Study',
                'description' => 'Real-world case studies and examples',
                'color' => '#6c757d',
                'is_active' => true,
            ],
            [
                'name' => 'Review',
                'description' => 'Product and service reviews',
                'color' => '#e83e8c',
                'is_active' => true,
            ],

            // Technologies
            [
                'name' => 'API',
                'description' => 'API development and integration',
                'color' => '#fd7e14',
                'is_active' => true,
            ],
            [
                'name' => 'Database',
                'description' => 'Database design and optimization',
                'color' => '#6610f2',
                'is_active' => true,
            ],
            [
                'name' => 'DevOps',
                'description' => 'DevOps practices and tools',
                'color' => '#20c997',
                'is_active' => true,
            ],
            [
                'name' => 'Security',
                'description' => 'Security practices and vulnerabilities',
                'color' => '#dc3545',
                'is_active' => true,
            ],
            [
                'name' => 'Performance',
                'description' => 'Performance optimization techniques',
                'color' => '#ffc107',
                'is_active' => true,
            ],

            // Tools
            [
                'name' => 'Git',
                'description' => 'Git version control',
                'color' => '#f05032',
                'is_active' => true,
            ],
            [
                'name' => 'Docker',
                'description' => 'Docker containerization',
                'color' => '#2496ed',
                'is_active' => true,
            ],
            [
                'name' => 'Composer',
                'description' => 'PHP dependency manager',
                'color' => '#885630',
                'is_active' => true,
            ],
            [
                'name' => 'npm',
                'description' => 'Node.js package manager',
                'color' => '#cb3837',
                'is_active' => true,
            ],

            // General
            [
                'name' => 'Beginner',
                'description' => 'Content suitable for beginners',
                'color' => '#28a745',
                'is_active' => true,
            ],
            [
                'name' => 'Advanced',
                'description' => 'Advanced level content',
                'color' => '#dc3545',
                'is_active' => true,
            ],
            [
                'name' => 'Open Source',
                'description' => 'Open source projects and tools',
                'color' => '#6f42c1',
                'is_active' => true,
            ],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['name' => $tag['name']],
                $tag
            );
        }
    }
}