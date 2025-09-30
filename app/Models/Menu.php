<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'location',
        'description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the menu items for the menu.
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    /**
     * Get the root menu items (items without parent).
     */
    public function rootMenuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('sort_order');
    }

    /**
     * Scope a query to only include active menus.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get menu by location.
     */
    public static function getByLocation(string $location): ?self
    {
        return static::where('location', $location)->where('is_active', true)->first();
    }

    /**
     * Get all menu locations.
     */
    public static function getLocations(): array
    {
        return [
            'header-primary' => 'Header Primary',
            'header-secondary' => 'Header Secondary',
            'footer-primary' => 'Footer Primary',
            'footer-secondary' => 'Footer Secondary',
            'sidebar' => 'Sidebar',
            'mobile' => 'Mobile',
        ];
    }
}
