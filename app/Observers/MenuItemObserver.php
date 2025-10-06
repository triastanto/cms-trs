<?php

namespace App\Observers;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Cache;

class MenuItemObserver
{
    /**
     * Handle the MenuItem "creating" event.
     */
    public function creating(MenuItem $menuItem): void
    {
        // Set sort order if not provided
        if (! $menuItem->sort_order) {
            $maxOrder = MenuItem::where('menu_id', $menuItem->menu_id)
                ->where('parent_id', $menuItem->parent_id)
                ->max('sort_order') ?? 0;

            $menuItem->sort_order = $maxOrder + 1;
        }
    }

    /**
     * Handle the MenuItem "created" event.
     */
    public function created(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Handle the MenuItem "updating" event.
     */
    public function updating(MenuItem $menuItem): void
    {
        // Auto-detect external links
        if ($menuItem->isDirty('url')) {
            $url = $menuItem->url;
            if ($url && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
                $menuItem->is_external = true;
                $menuItem->target = '_blank';
            }
        }
    }

    /**
     * Handle the MenuItem "updated" event.
     */
    public function updated(MenuItem $menuItem): void
    {
        $this->clearMenuCache($menuItem);
    }

    /**
     * Handle the MenuItem "deleted" event.
     */
    public function deleted(MenuItem $menuItem): void
    {
        // Reorder remaining items
        $this->reorderSiblings($menuItem);
        $this->clearMenuCache($menuItem);
    }

    /**
     * Handle the MenuItem "restored" event.
     */
    public function restored(MenuItem $menuItem): void
    {
        //
    }

    /**
     * Handle the MenuItem "force deleted" event.
     */
    public function forceDeleted(MenuItem $menuItem): void
    {
        // Reorder remaining items
        $this->reorderSiblings($menuItem);
    }

    /**
     * Reorder sibling menu items after deletion.
     */
    private function reorderSiblings(MenuItem $deletedItem): void
    {
        $siblings = MenuItem::where('menu_id', $deletedItem->menu_id)
            ->where('parent_id', $deletedItem->parent_id)
            ->where('id', '!=', $deletedItem->id)
            ->orderBy('sort_order')
            ->get();

        foreach ($siblings as $index => $sibling) {
            $sibling->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Clear menu cache for the affected menu location.
     */
    private function clearMenuCache(MenuItem $menuItem): void
    {
        $menu = $menuItem->menu;
        if ($menu && $menu->location) {
            Cache::forget("menu.location.{$menu->location}");
        }
    }
}
