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
        it('can render the create post page', function () {
            $response = $this->get('/admin/posts/create');

            $response->assertStatus(200);
            $response->assertSee('Create Post');
        });

        it('can create a post with tags using factories', function () {
            $post = Post::factory()->create();
            $tags = Tag::factory()->count(2)->create();

            $post->tags()->attach($tags->pluck('id'));

            expect($post->tags)->toHaveCount(2);
        });

        it('can create a post with additional categories using factories', function () {
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
        it('can render edit page for draft posts', function () {
            $post = Post::factory()->create(['status' => 'draft']);

            $response = $this->get("/admin/posts/{$post->slug}/edit");

            $response->assertStatus(200);
            $response->assertSee($post->title);
        });

        it('can render edit page for published posts', function () {
            $post = Post::factory()->create(['status' => 'published']);

            $response = $this->get("/admin/posts/{$post->slug}/edit");

            $response->assertStatus(200);
            $response->assertSee($post->title);
        });
    });

    describe('Bulk Actions', function () {
        it('can create multiple posts using factories', function () {
            $posts = Post::factory()->count(3)->create(['status' => 'draft']);

            expect($posts)->toHaveCount(3);

            foreach ($posts as $post) {
                $this->assertDatabaseHas('posts', [
                    'id' => $post->id,
                    'status' => 'draft',
                ]);
            }
        });

        it('can create posts with different statuses', function () {
            $draftPost = Post::factory()->create(['status' => 'draft']);
            $publishedPost = Post::factory()->create(['status' => 'published']);
            $archivedPost = Post::factory()->create(['status' => 'archived']);

            $this->assertDatabaseHas('posts', ['id' => $draftPost->id, 'status' => 'draft']);
            $this->assertDatabaseHas('posts', ['id' => $publishedPost->id, 'status' => 'published']);
            $this->assertDatabaseHas('posts', ['id' => $archivedPost->id, 'status' => 'archived']);
        });
    });
});
