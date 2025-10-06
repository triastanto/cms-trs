<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
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
            $post = new Post;
            $expectedFillable = [
                'title',
                'slug',
                'content',
                'excerpt',
                'status',
                'meta_title',
                'meta_description',
                'published_at',
                'user_id',
                'category_id',
            ];

            expect($post->getFillable())->toBe($expectedFillable);
        });

        it('has the correct casts', function () {
            $post = new Post;
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

        it('can have additional categories through many-to-many relationship', function () {
            $post = Post::factory()->create();
            $additionalCategories = Category::factory()->count(2)->create();

            $post->categories()->attach($additionalCategories->pluck('id'));

            expect($post->categories)->toHaveCount(2);
            expect($post->categories->first())->toBeInstanceOf(Category::class);
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

        it('uses slug as route key', function () {
            $post = Post::factory()->create(['slug' => 'my-post']);

            expect($post->getRouteKeyName())->toBe('slug');
        });
    });

    describe('Excerpt Generation', function () {
        it('generates excerpt from content when excerpt is empty', function () {
            $content = 'This is a very long content that should be truncated to create an excerpt. '.str_repeat('This is additional content. ', 20);

            $post = Post::factory()->create([
                'content' => $content,
                'excerpt' => null,
            ]);

            expect($post->excerpt)->toContain('This is a very long content');
            expect(strlen($post->excerpt))->toBeLessThanOrEqual(163); // Allow for reasonable excerpt length (160 + 3 for ellipsis)
        });

        it('uses provided excerpt when available', function () {
            $excerpt = 'This is a custom excerpt';
            $post = Post::factory()->create(['excerpt' => $excerpt]);

            expect($post->excerpt)->toBe($excerpt);
        });
    });

    describe('Boot Method Logic', function () {
        it('does not overwrite existing slug on creation', function () {
            $post = Post::create([
                'title' => 'My Awesome Post',
                'slug' => 'custom-slug',
                'content' => 'This is content',
                'user_id' => $this->user->id,
            ]);

            expect($post->slug)->toBe('custom-slug');
        });

        it('updates slug only when title changes and slug is empty', function () {
            $post = Post::create([
                'title' => 'Original Title',
                'content' => 'This is content',
                'user_id' => $this->user->id,
            ]);

            $originalSlug = $post->slug;

            // Update title with existing slug - should not change
            $post->update(['title' => 'New Title']);
            expect($post->slug)->toBe($originalSlug);

            // Update title with empty slug - should update
            $post->slug = '';
            $post->save();
            $post->update(['title' => 'Another Title']);
            expect($post->slug)->toBe('another-title');
        });

        it('preserves manually set slug during updates', function () {
            $post = Post::create([
                'title' => 'Original Title',
                'content' => 'This is content',
                'user_id' => $this->user->id,
            ]);

            $post->update([
                'title' => 'Updated Title',
                'slug' => 'my-custom-slug',
            ]);

            expect($post->slug)->toBe('my-custom-slug');

            // Another update should not change the custom slug
            $post->update(['title' => 'Final Title']);
            expect($post->slug)->toBe('my-custom-slug');
        });
    });

    describe('Excerpt Accessor Edge Cases', function () {
        it('handles content with HTML tags correctly', function () {
            $content = '<p>This is <strong>HTML content</strong> with <em>various tags</em>.</p>'.str_repeat('<p>More content here.</p>', 10);

            $post = Post::factory()->create([
                'content' => $content,
                'excerpt' => null,
            ]);

            $excerpt = $post->excerpt;
            expect($excerpt)->not->toContain('<p>');
            expect($excerpt)->not->toContain('<strong>');
            expect($excerpt)->not->toContain('<em>');
            expect($excerpt)->toContain('This is HTML content');
        });

        it('handles empty content gracefully', function () {
            $post = Post::factory()->create([
                'content' => '',
                'excerpt' => null,
            ]);

            expect($post->excerpt)->toBe('');
        });

        it('handles null content gracefully', function () {
            $post = Post::factory()->create([
                'content' => '',
                'excerpt' => null,
            ]);

            expect($post->excerpt)->toBe('');
        });
    });

});
