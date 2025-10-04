<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Get all categories and tags
        $categories = Category::where('is_active', true)->get();
        $tags = Tag::where('is_active', true)->get();
        $users = User::all();

        if ($categories->isEmpty() || $tags->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Make sure Categories, Tags, and Users are seeded first!');

            return;
        }

        $posts = [
            [
                'title' => 'Getting Started with Laravel 11: A Complete Guide',
                'content' => '<p>Laravel 11 brings exciting new features and improvements that make web development even more enjoyable. In this comprehensive guide, we\'ll explore the key features, installation process, and best practices for getting started with Laravel 11.</p><h2>What\'s New in Laravel 11</h2><p>Laravel 11 introduces several groundbreaking features including improved performance, enhanced security, and better developer experience. The new release focuses on simplicity and developer productivity.</p><h2>Installation and Setup</h2><p>Getting started with Laravel 11 is straightforward. Follow these steps to create your first Laravel 11 application and start building amazing web applications.</p>',
                'excerpt' => 'Discover the exciting features of Laravel 11 and learn how to build modern web applications with this comprehensive guide.',
                'status' => 'published',
                'published_at' => now()->subDays(7),
                'category' => 'Web Development',
                'tags' => ['Laravel', 'PHP', 'Tutorial', 'Beginner'],
            ],
            [
                'title' => 'Building RESTful APIs with Laravel Sanctum',
                'content' => '<p>API authentication is a crucial aspect of modern web development. Laravel Sanctum provides a featherweight authentication system for SPAs, mobile applications, and simple token-based APIs.</p><h2>Setting Up Sanctum</h2><p>Installing and configuring Laravel Sanctum is straightforward. We\'ll walk through the entire setup process and implement secure API endpoints.</p><h2>Token Management</h2><p>Learn how to create, manage, and revoke API tokens effectively to maintain security in your applications.</p>',
                'excerpt' => 'Learn how to build secure RESTful APIs using Laravel Sanctum for token-based authentication.',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'category' => 'Web Development',
                'tags' => ['Laravel', 'API', 'Security', 'Tutorial'],
            ],
            [
                'title' => 'React Hooks: Modern State Management Patterns',
                'content' => '<p>React Hooks have revolutionized how we manage state and side effects in React applications. This guide explores advanced patterns and best practices for using hooks effectively.</p><h2>Custom Hooks</h2><p>Creating custom hooks allows you to extract component logic into reusable functions. We\'ll build several practical examples that you can use in your own projects.</p><h2>Performance Optimization</h2><p>Learn how to optimize your React applications using useMemo, useCallback, and other performance-focused hooks.</p>',
                'excerpt' => 'Master React Hooks with advanced patterns and learn how to build more efficient React applications.',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'category' => 'Web Development',
                'tags' => ['React', 'JavaScript', 'Tutorial', 'Advanced'],
            ],
            [
                'title' => 'Database Optimization Strategies for High-Traffic Applications',
                'content' => '<p>As your application grows, database performance becomes critical. This comprehensive guide covers essential optimization strategies to handle high-traffic scenarios efficiently.</p><h2>Indexing Strategies</h2><p>Proper indexing is fundamental to database performance. Learn how to create and optimize indexes for different query patterns.</p><h2>Query Optimization</h2><p>Discover techniques to write efficient queries and avoid common performance pitfalls that can slow down your application.</p>',
                'excerpt' => 'Essential database optimization techniques to handle high-traffic applications and improve performance.',
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'category' => 'Technology',
                'tags' => ['Database', 'Performance', 'Best Practices', 'Advanced'],
            ],
            [
                'title' => 'Modern CSS: Grid vs Flexbox - When to Use Each',
                'content' => '<p>CSS Grid and Flexbox are powerful layout systems that have transformed modern web design. Understanding when to use each one is crucial for creating efficient and maintainable layouts.</p><h2>CSS Grid Fundamentals</h2><p>CSS Grid excels at creating two-dimensional layouts. Learn the core concepts and practical applications of CSS Grid.</p><h2>Flexbox Mastery</h2><p>Flexbox is perfect for one-dimensional layouts and component alignment. Master the flexbox model and its various properties.</p>',
                'excerpt' => 'Learn the differences between CSS Grid and Flexbox, and discover when to use each layout method.',
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'category' => 'Design',
                'tags' => ['CSS', 'Design', 'Tutorial', 'Best Practices'],
            ],
            [
                'title' => 'Docker for PHP Developers: Complete Development Environment',
                'content' => '<p>Docker simplifies development environment setup and ensures consistency across different machines. This guide shows PHP developers how to leverage Docker for their projects.</p><h2>Setting Up PHP with Docker</h2><p>Create a complete PHP development environment using Docker containers. We\'ll cover PHP, Nginx, MySQL, and other essential services.</p><h2>Docker Compose</h2><p>Learn how to orchestrate multiple containers using Docker Compose for complex application setups.</p>',
                'excerpt' => 'Set up a complete PHP development environment using Docker and Docker Compose.',
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'category' => 'Technology',
                'tags' => ['Docker', 'PHP', 'DevOps', 'Tutorial'],
            ],
            [
                'title' => 'JavaScript Performance Optimization Techniques',
                'content' => '<p>JavaScript performance is crucial for user experience. This article covers proven techniques to optimize your JavaScript code and improve application performance.</p><h2>Memory Management</h2><p>Understanding JavaScript\'s memory model helps prevent memory leaks and optimize garbage collection.</p><h2>Async Patterns</h2><p>Learn how to use async/await, promises, and other asynchronous patterns effectively to improve performance.</p>',
                'excerpt' => 'Essential JavaScript performance optimization techniques for faster web applications.',
                'status' => 'published',
                'published_at' => now()->subDays(6),
                'category' => 'Web Development',
                'tags' => ['JavaScript', 'Performance', 'Best Practices', 'Advanced'],
            ],
            [
                'title' => 'Building Scalable Microservices with PHP',
                'content' => '<p>Microservices architecture offers scalability and flexibility for modern applications. Learn how to design and implement microservices using PHP and related technologies.</p><h2>Service Design Principles</h2><p>Understand the key principles of microservices design including service boundaries, data management, and communication patterns.</p><h2>Implementation Strategies</h2><p>Practical implementation techniques using PHP frameworks and tools for building robust microservices.</p>',
                'excerpt' => 'Learn how to design and implement scalable microservices architecture using PHP.',
                'status' => 'draft',
                'published_at' => null,
                'category' => 'Technology',
                'tags' => ['PHP', 'Architecture', 'Microservices', 'Advanced'],
            ],
            [
                'title' => 'Vue.js 3 Composition API: Complete Guide',
                'content' => '<p>Vue.js 3\'s Composition API introduces a new way to organize and reuse component logic. This comprehensive guide covers everything you need to know about the Composition API.</p><h2>Reactive References</h2><p>Learn how to use ref and reactive to create reactive state in your Vue.js applications.</p><h2>Composables</h2><p>Discover how to create reusable composables to share logic between components effectively.</p>',
                'excerpt' => 'Master Vue.js 3 Composition API with practical examples and best practices.',
                'status' => 'published',
                'published_at' => now()->subDays(4),
                'category' => 'Web Development',
                'tags' => ['Vue.js', 'JavaScript', 'Tutorial', 'Advanced'],
            ],
            [
                'title' => 'TypeScript Best Practices for Large Applications',
                'content' => '<p>TypeScript helps build maintainable and scalable applications. This guide covers best practices for using TypeScript effectively in large-scale projects.</p><h2>Type Design</h2><p>Learn how to design robust type systems that provide safety without hindering development productivity.</p><h2>Advanced Types</h2><p>Explore advanced TypeScript features including generics, conditional types, and mapped types.</p>',
                'excerpt' => 'Essential TypeScript best practices for building large-scale, maintainable applications.',
                'status' => 'published',
                'published_at' => now()->subDays(9),
                'category' => 'Web Development',
                'tags' => ['TypeScript', 'JavaScript', 'Best Practices', 'Advanced'],
            ],
            [
                'title' => 'Secure Coding Practices for Web Applications',
                'content' => '<p>Security should be a top priority in web development. This comprehensive guide covers essential secure coding practices to protect your applications from common vulnerabilities.</p><h2>Input Validation</h2><p>Learn how to properly validate and sanitize user input to prevent injection attacks and other security issues.</p><h2>Authentication & Authorization</h2><p>Implement robust authentication and authorization systems to protect sensitive data and functionality.</p>',
                'excerpt' => 'Essential security practices every web developer should know to build secure applications.',
                'status' => 'published',
                'published_at' => now()->subDays(11),
                'category' => 'Technology',
                'tags' => ['Security', 'Best Practices', 'Tutorial', 'Advanced'],
            ],
            [
                'title' => 'Git Workflow Strategies for Team Development',
                'content' => '<p>Effective Git workflows are essential for team collaboration. This guide explores different Git strategies and helps you choose the right approach for your team.</p><h2>Branching Strategies</h2><p>Compare Git Flow, GitHub Flow, and other popular branching strategies to find the best fit for your team.</p><h2>Code Review Process</h2><p>Establish effective code review processes using Git and modern development platforms.</p>',
                'excerpt' => 'Choose the right Git workflow strategy for your development team and improve collaboration.',
                'status' => 'published',
                'published_at' => now()->subDays(13),
                'category' => 'Technology',
                'tags' => ['Git', 'DevOps', 'Best Practices', 'Tutorial'],
            ],
        ];

        foreach ($posts as $postData) {
            // Find category
            $category = $categories->where('name', $postData['category'])->first();

            // Create post
            $post = Post::create([
                'title' => $postData['title'],
                'content' => $postData['content'],
                'excerpt' => $postData['excerpt'],
                'status' => $postData['status'],
                'published_at' => $postData['published_at'],
                'meta_title' => $postData['title'],
                'meta_description' => $postData['excerpt'],
                'user_id' => $users->random()->id,
                'category_id' => $category ? $category->id : null,
            ]);

            // Attach random tags from the specified tags
            $postTags = $tags->whereIn('name', $postData['tags']);
            if ($postTags->isNotEmpty()) {
                $post->tags()->attach($postTags->pluck('id')->toArray());
            }
        }

        // Create additional random posts using factories if available
        $this->createRandomPosts($categories, $tags, $users, 15);
    }

    private function createRandomPosts($categories, $tags, $users, $count)
    {
        $faker = Faker::create();

        for ($i = 0; $i < $count; $i++) {
            $status = $faker->randomElement(['published', 'draft', 'archived']);
            $publishedAt = $status === 'published' ? $faker->dateTimeBetween('-30 days', 'now') : null;

            $post = Post::create([
                'title' => $faker->sentence(6),
                'content' => '<p>'.implode('</p><p>', $faker->paragraphs(5)).'</p>',
                'excerpt' => $faker->text(150),
                'status' => $status,
                'published_at' => $publishedAt,
                'meta_title' => $faker->sentence(4),
                'meta_description' => $faker->text(160),
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
            ]);

            // Attach 1-4 random tags
            $randomTags = $tags->random($faker->numberBetween(1, 4));
            $post->tags()->attach($randomTags->pluck('id')->toArray());
        }
    }
}
