<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'target',
        'icon',
        'css_class',
        'sort_order',
        'is_active',
        'is_external',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_external' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the menu that owns the menu item.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the parent menu item.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get the child menu items.
     */
    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get all descendants (children, grandchildren, etc.).
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope a query to only include active menu items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root menu items (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check if the menu item has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Check if the menu item is external.
     */
    public function isExternal(): bool
    {
        return $this->is_external || ($this->target === '_blank');
    }

    /**
     * Get the full URL with proper protocol.
     */
    public function getFullUrlAttribute(): string
    {
        if ($this->isExternal() && ! str_starts_with($this->url, 'http')) {
            return 'https://'.ltrim($this->url, '/');
        }

        return $this->url ?? '#';
    }

    /**
     * Get target options.
     */
    public static function getTargetOptions(): array
    {
        return [
            '_self' => 'Same Window',
            '_blank' => 'New Window',
            '_parent' => 'Parent Window',
            '_top' => 'Top Window',
        ];
    }
}
