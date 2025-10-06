<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected $attributes = [
        'color' => '#3B82F6',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
            if (empty($category->color)) {
                $category->color = setting('default_category_color', '#3B82F6');
            }
        });

        // Slug updates are now optional - only update if slug is empty
        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getFullPathAttribute(): string
    {
        $path = $this->name;
        $parent = $this->parent;
        while ($parent) {
            $path = $parent->name.' > '.$path;
            $parent = $parent->parent;
        }

        return $path;
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        // Check if media conversions should be queued (production only)
        $shouldQueue = config('performance.queue.media_conversions', false);

        $this->addMediaCollection('thumbnail')
            ->useDisk('public')
            ->singleFile()
            ->registerMediaConversions(function () use ($shouldQueue) {
                $conversion = $this->addMediaConversion('thumb')
                    ->width(200)
                    ->height(200)
                    ->sharpen(10);

                if ($shouldQueue) {
                    $conversion->queued();
                }
            });
    }
}
