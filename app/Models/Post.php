<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the post.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the tags associated with the post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the additional categories associated with the post.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the post's status options.
     */
    public static function getStatusOptions(): array
    {
        return [
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->status)) {
                $post->status = setting('default_post_status', 'draft');
            }
        });

        // Slug updates are now optional - only update if slug is empty
        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope a query to only include published posts.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft posts.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Get the excerpt or generate from content.
     */
    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }

        if (! $this->content) {
            return '';
        }

        // Use settings for auto-excerpt generation
        if (setting('auto_generate_excerpts', true)) {
            return Str::limit(strip_tags($this->content), setting('excerpt_length', 160));
        }

        return '';
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        // Check if media conversions should be queued (production only)
        $shouldQueue = config('performance.queue.media_conversions', false);

        $this->addMediaCollection('post_images')
            ->useDisk('public')
            ->registerMediaConversions(function () use ($shouldQueue) {
                $conversion = $this->addMediaConversion('thumb')
                    ->width(setting('thumbnail_width', 368))
                    ->height(setting('thumbnail_height', 232))
                    ->sharpen(10);

                if ($shouldQueue) {
                    $conversion->queued();
                }

                $previewConversion = $this->addMediaConversion('preview')
                    ->width(800)
                    ->height(600)
                    ->sharpen(10);

                if ($shouldQueue) {
                    $previewConversion->queued();
                }
            });
    }

    /**
     * Get the featured image.
     */
    public function getFeaturedImage()
    {
        return $this->getMedia('post_images')
            ->first(fn ($media) => $media->getCustomProperty('is_featured') === true);
    }

    /**
     * Get all gallery images (non-featured).
     */
    public function getGalleryImages()
    {
        return $this->getMedia('post_images')
            ->filter(fn ($media) => $media->getCustomProperty('is_featured') !== true);
    }

    /**
     * Set a specific image as featured, unsetting others.
     */
    public function setFeaturedImage($mediaId): void
    {
        // Unset all featured flags
        foreach ($this->getMedia('post_images') as $media) {
            if ($media->getCustomProperty('is_featured') === true) {
                $media->setCustomProperty('is_featured', false);
                $media->save();
            }
        }

        // Set the specified media as featured
        $media = $this->getMedia('post_images')->firstWhere('id', $mediaId);
        if ($media) {
            $media->setCustomProperty('is_featured', true);
            $media->save();
        }
    }

    /**
     * Unset featured image.
     */
    public function unsetFeaturedImage(): void
    {
        foreach ($this->getMedia('post_images') as $media) {
            if ($media->getCustomProperty('is_featured') === true) {
                $media->setCustomProperty('is_featured', false);
                $media->save();
            }
        }
    }
}
