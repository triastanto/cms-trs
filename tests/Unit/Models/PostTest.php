<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Post Model', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->tag = Tag::factory()->create();
    });

    describe('Basic Attributes', function () {
        it('has the correct fillable attributes', function () {
            $post = new Post();
            $expectedFillable = [
                'title',
                'slug',
                'content',
                'excerpt',
                'status',
                'featured_image',
                'meta_title',
                'meta_description',
                'published_at',
                'user_id',
                'category_id',
            ];

            expect($post->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $post = new Post();
            $expectedCasts = [
                'published_at' => 'datetime',
                'created_at' => 'datetime',
                'updated_at' => 'datetime',
            ];

            expect($post->getCasts())->toMatchArray($expectedCasts);
        });
    });

    describe('Relationships', function () {
        it('belongs to a user', function () {
            $post = Post::factory()->create(['user_id' => $this->user->id]);

            expect($post->user)->toBeInstanceOf(User::class);
            expect($post->user->id)->toBe($this->user->id);
        });

        it('belongs to a category', function () {
            $post = Post::factory()->create(['category_id' => $this->category->id]);

            expect($post->category)->toBeInstanceOf(Category::class);
            expect($post->category->id)->toBe($this->category->id);
        });

        it('belongs to many categories', function () {
            $post = Post::factory()->create();
            $categories = Category::factory()->count(3)->create();
            
            $post->categories()->attach($categories->pluck('id'));

            expect($post->categories)->toHaveCount(3);
            expect($post->categories->first())->toBeInstanceOf(Category::class);
        });

        it('belongs to many tags', function () {
            $post = Post::factory()->create();
            $tags = Tag::factory()->count(2)->create();
            
            $post->tags()->attach($tags->pluck('id'));

            expect($post->tags)->toHaveCount(2);
            expect($post->tags->first())->toBeInstanceOf(Tag::class);
        });
    });

    describe('Status Options', function () {
        it('returns correct status options', function () {
            $statusOptions = Post::getStatusOptions();

            expect($statusOptions)->toBe([
                'draft' => 'Draft',
                'published' => 'Published',
                'archived' => 'Archived',
            ]);
        });
    });

    describe('Scopes', function () {
        it('can scope to published posts', function () {
            Post::factory()->create(['status' => 'draft']);
            Post::factory()->create(['status' => 'published', 'published_at' => now()]);
            Post::factory()->create(['status' => 'published', 'published_at' => now()->addDay()]);

            $publishedPosts = Post::published()->get();

            expect($publishedPosts)->toHaveCount(1);
            expect($publishedPosts->first()->status)->toBe('published');
        });

        it('can scope to draft posts', function () {
            Post::factory()->create(['status' => 'draft']);
            Post::factory()->create(['status' => 'published']);

            $draftPosts = Post::draft()->get();

            expect($draftPosts)->toHaveCount(1);
            expect($draftPosts->first()->status)->toBe('draft');
        });
    });

    describe('Slug Generation', function () {
        it('generates slug from title on creation', function () {
            $post = Post::create([
                'title' => 'My Awesome Post',
                'content' => 'This is content',
                'user_id' => $this->user->id,
            ]);

            expect($post->slug)->toBe('my-awesome-post');
        });

        it('updates slug when title changes', function () {
            $post = Post::factory()->create(['title' => 'Original Title']);

            $post->update(['title' => 'Updated Title']);

            expect($post->fresh()->slug)->toBe('updated-title');
        });

        it('uses slug as route key', function () {
            $post = Post::factory()->create(['slug' => 'my-post']);

            expect($post->getRouteKeyName())->toBe('slug');
        });
    });

    describe('Excerpt Generation', function () {
        it('generates excerpt from content when excerpt is empty', function () {
            $content = 'This is a very long content that should be truncated to create an excerpt. ' . str_repeat('This is additional content. ', 20);
            
            $post = Post::factory()->create([
                'content' => $content,
                'excerpt' => null,
            ]);

            expect($post->excerpt)->toContain('This is a very long content');
            expect(strlen($post->excerpt))->toBeLessThanOrEqual(150);
        });

        it('uses provided excerpt when available', function () {
            $excerpt = 'This is a custom excerpt';
            $post = Post::factory()->create(['excerpt' => $excerpt]);

            expect($post->excerpt)->toBe($excerpt);
        });
    });

    describe('Related Posts', function () {
        it('can find related posts based on categories and tags', function () {
            $post = Post::factory()->create();
            $category = Category::factory()->create();
            $tag = Tag::factory()->create();
            
            $post->categories()->attach($category->id);
            $post->tags()->attach($tag->id);

            $relatedPost = Post::factory()->create();
            $relatedPost->categories()->attach($category->id);

            $relatedPosts = $post->relatedPosts();

            expect($relatedPosts)->toHaveCount(1);
            expect($relatedPosts->first()->id)->toBe($relatedPost->id);
        });

        it('excludes current post from related posts', function () {
            $post = Post::factory()->create();
            $category = Category::factory()->create();
            
            $post->categories()->attach($category->id);

            $relatedPosts = $post->relatedPosts();

            expect($relatedPosts->pluck('id'))->not->toContain($post->id);
        });

        it('limits related posts to specified number', function () {
            $post = Post::factory()->create();
            $category = Category::factory()->create();
            
            $post->categories()->attach($category->id);

            Post::factory()->count(10)->create()->each(function ($relatedPost) use ($category) {
                $relatedPost->categories()->attach($category->id);
            });

            $relatedPosts = $post->relatedPosts(3);

            expect($relatedPosts)->toHaveCount(3);
        });
    });
});
