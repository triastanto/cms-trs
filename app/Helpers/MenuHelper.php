<?php

namespace App\Helpers;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Collection;

class MenuHelper
{
    /**
     * Get menu by location with all active items.
     */
    public static function getMenuByLocation(string $location): ?Menu
    {
        return Menu::with(['menuItems' => function ($query) {
            $query->active()->orderBy('sort_order');
        }])->where('location', $location)->where('is_active', true)->first();
    }

    /**
     * Get menu items for a specific location.
     */
    public static function getMenuItems(string $location): Collection
    {
        $menu = self::getMenuByLocation($location);

        if (! $menu) {
            return collect();
        }

        return $menu->menuItems->whereNull('parent_id');
    }

    /**
     * Get all menu items for a location (including children).
     */
    public static function getAllMenuItems(string $location): Collection
    {
        $menu = self::getMenuByLocation($location);

        if (! $menu) {
            return collect();
        }

        return $menu->menuItems;
    }

    /**
     * Build hierarchical menu structure.
     */
    public static function buildMenuTree(string $location): Collection
    {
        $menuItems = self::getAllMenuItems($location);

        return self::buildTree($menuItems);
    }

    /**
     * Build tree structure from flat collection.
     */
    private static function buildTree(Collection $items, ?int $parentId = null): Collection
    {
        return $items
            ->where('parent_id', $parentId)
            ->map(function ($item) use ($items) {
                $item->children = self::buildTree($items, $item->id);

                return $item;
            });
    }

    /**
     * Get breadcrumb trail for current page.
     */
    public static function getBreadcrumbs(string $currentPath): Collection
    {
        $breadcrumbs = collect();

        // Find menu items that match the current path
        $menuItems = MenuItem::where('url', $currentPath)
            ->where('is_active', true)
            ->with(['menu', 'parent'])
            ->get();

        foreach ($menuItems as $item) {
            $trail = self::buildBreadcrumbTrail($item);
            if ($trail->isNotEmpty()) {
                $breadcrumbs = $trail;
                break;
            }
        }

        return $breadcrumbs;
    }

    /**
     * Build breadcrumb trail for a menu item.
     */
    private static function buildBreadcrumbTrail(MenuItem $item, ?Collection $trail = null): Collection
    {
        if ($trail === null) {
            $trail = collect();
        }

        $trail->prepend($item);

        if ($item->parent instanceof MenuItem) {
            return self::buildBreadcrumbTrail($item->parent, $trail);
        }

        return $trail;
    }

    /**
     * Check if a menu item is active (current page).
     */
    public static function isActiveMenuItem(MenuItem $item, string $currentPath): bool
    {
        if ($item->url === $currentPath) {
            return true;
        }

        // Check if any child is active
        return $item->children->contains(function ($child) use ($currentPath) {
            if ($child instanceof MenuItem) {
                return self::isActiveMenuItem($child, $currentPath);
            }

            return false;
        });
    }

    /**
     * Get active menu items for a location.
     */
    public static function getActiveMenuItems(string $location, string $currentPath): Collection
    {
        $menuItems = self::getMenuItems($location);

        return $menuItems->filter(function ($item) use ($currentPath) {
            return self::isActiveMenuItem($item, $currentPath);
        });
    }

    /**
     * Get menu statistics.
     */
    public static function getMenuStats(): array
    {
        return [
            'total_menus' => Menu::count(),
            'active_menus' => Menu::where('is_active', true)->count(),
            'total_menu_items' => MenuItem::count(),
            'active_menu_items' => MenuItem::where('is_active', true)->count(),
            'external_menu_items' => MenuItem::where('is_external', true)->count(),
        ];
    }

    /**
     * Get menu locations with item counts.
     */
    public static function getMenuLocationsWithCounts(): array
    {
        $locations = Menu::getLocations();
        $result = [];

        foreach ($locations as $key => $name) {
            $menu = Menu::where('location', $key)->first();
            $result[$key] = [
                'name' => $name,
                'exists' => $menu !== null,
                'active' => $menu !== null ? $menu->is_active : false,
                'item_count' => $menu?->menuItems()->count() ?? 0,
            ];
        }

        return $result;
    }

    /**
     * Duplicate a menu with all its items.
     */
    public static function duplicateMenu(Menu $menu, string $newName, string $newLocation): Menu
    {
        $newMenu = $menu->replicate();
        $newMenu->name = $newName;
        $newMenu->location = $newLocation;
        $newMenu->save();

        // Duplicate menu items
        self::duplicateMenuItems($menu->menuItems, $newMenu->id);

        return $newMenu;
    }

    /**
     * Duplicate menu items recursively.
     */
    private static function duplicateMenuItems(Collection $items, int $newMenuId, ?int $newParentId = null): void
    {
        foreach ($items as $item) {
            $newItem = $item->replicate();
            $newItem->menu_id = $newMenuId;
            $newItem->parent_id = $newParentId;
            $newItem->save();

            if ($item->children->isNotEmpty()) {
                self::duplicateMenuItems($item->children, $newMenuId, $newItem->id);
            }
        }
    }

    /**
     * Reorder menu items.
     */
    public static function reorderMenuItems(array $itemIds): void
    {
        foreach ($itemIds as $index => $itemId) {
            MenuItem::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Move menu item to different parent.
     */
    public static function moveMenuItem(MenuItem $item, ?int $newParentId = null): void
    {
        $item->parent_id = $newParentId;
        $item->save();

        // Reorder items in the new parent
        $siblings = MenuItem::where('menu_id', $item->menu_id)
            ->where('parent_id', $newParentId)
            ->where('id', '!=', $item->id)
            ->orderBy('sort_order')
            ->get();

        $maxOrder = $siblings->max('sort_order') ?? 0;
        $item->sort_order = $maxOrder + 1;
        $item->save();
    }
}
