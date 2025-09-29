<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Post Resource with Content Organization', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        $this->category = Category::factory()->create();
        $this->tag = Tag::factory()->create();
    });

    describe('Post Creation with Categories and Tags', function () {
        it('can create a post with primary category', function () {
            $postData = [
                'title' => 'Test Post',
                'slug' => 'test-post',
                'content' => 'This is test content',
                'status' => 'draft',
                'user_id' => $this->user->id,
                'category_id' => $this->category->id,
            ];

            $response = $this->post('/admin/posts', $postData);

            $response->assertRedirect();
            $this->assertDatabaseHas('posts', [
                'title' => 'Test Post',
                'category_id' => $this->category->id,
            ]);
        });

        it('can create a post with tags', function () {
            $post = Post::factory()->create();
            $tags = Tag::factory()->count(2)->create();

            $post->tags()->attach($tags->pluck('id'));

            expect($post->tags)->toHaveCount(2);
        });

        it('can create a post with additional categories', function () {
            $post = Post::factory()->create();
            $categories = Category::factory()->count(3)->create();

            $post->categories()->attach($categories->pluck('id'));

            expect($post->categories)->toHaveCount(3);
        });
    });

    describe('Post Filtering', function () {
        it('can filter posts by category', function () {
            $post1 = Post::factory()->create(['category_id' => $this->category->id]);
            $post2 = Post::factory()->create();

            $response = $this->get("/admin/posts?tableFilters[category_id][value]={$this->category->id}");

            $response->assertStatus(200);
        });

        it('can filter posts by tags', function () {
            $post = Post::factory()->create();
            $post->tags()->attach($this->tag->id);

            $response = $this->get("/admin/posts?tableFilters[tags][value]={$this->tag->id}");

            $response->assertStatus(200);
        });
    });

    describe('Post Status Management', function () {
        it('can publish posts', function () {
            $post = Post::factory()->create(['status' => 'draft']);

            $response = $this->put("/admin/posts/{$post->id}", [
                'title' => $post->title,
                'content' => $post->content,
                'status' => 'published',
                'published_at' => now(),
                'user_id' => $this->user->id,
            ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('posts', [
                'id' => $post->id,
                'status' => 'published',
            ]);
        });

        it('can archive posts', function () {
            $post = Post::factory()->create(['status' => 'published']);

            $response = $this->put("/admin/posts/{$post->id}", [
                'title' => $post->title,
                'content' => $post->content,
                'status' => 'archived',
                'user_id' => $this->user->id,
            ]);

            $response->assertRedirect();
            $this->assertDatabaseHas('posts', [
                'id' => $post->id,
                'status' => 'archived',
            ]);
        });
    });

    describe('Bulk Actions', function () {
        it('can bulk publish posts', function () {
            $posts = Post::factory()->count(3)->create(['status' => 'draft']);

            $response = $this->post('/admin/posts/bulk-actions', [
                'action' => 'publish',
                'records' => $posts->pluck('id')->toArray(),
            ]);

            $response->assertRedirect();
            
            foreach ($posts as $post) {
                $this->assertDatabaseHas('posts', [
                    'id' => $post->id,
                    'status' => 'published',
                ]);
            }
        });

        it('can bulk archive posts', function () {
            $posts = Post::factory()->count(3)->create(['status' => 'published']);

            $response = $this->post('/admin/posts/bulk-actions', [
                'action' => 'archive',
                'records' => $posts->pluck('id')->toArray(),
            ]);

            $response->assertRedirect();
            
            foreach ($posts as $post) {
                $this->assertDatabaseHas('posts', [
                    'id' => $post->id,
                    'status' => 'archived',
                ]);
            }
        });
    });
});
