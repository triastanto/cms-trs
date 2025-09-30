<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Header Primary Menu
        $headerMenu = Menu::create([
            'name' => 'Header Primary',
            'location' => 'header-primary',
            'description' => 'Main navigation menu for the header',
            'is_active' => true,
        ]);

        // Create Header Primary Menu Items
        $homeItem = MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'Home',
            'url' => '/',
            'target' => '_self',
            'icon' => 'home',
            'sort_order' => 1,
            'is_active' => true,
            'is_external' => false,
        ]);

        $aboutItem = MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'About',
            'url' => '/about',
            'target' => '_self',
            'icon' => 'info',
            'sort_order' => 2,
            'is_active' => true,
            'is_external' => false,
        ]);

        $servicesItem = MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'Services',
            'url' => '/services',
            'target' => '_self',
            'icon' => 'cog',
            'sort_order' => 3,
            'is_active' => true,
            'is_external' => false,
        ]);

        // Create Services submenu
        $webDevItem = MenuItem::create([
            'menu_id' => $headerMenu->id,
            'parent_id' => $servicesItem->id,
            'title' => 'Web Development',
            'url' => '/services/web-development',
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'is_external' => false,
        ]);

        $mobileDevItem = MenuItem::create([
            'menu_id' => $headerMenu->id,
            'parent_id' => $servicesItem->id,
            'title' => 'Mobile Development',
            'url' => '/services/mobile-development',
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'is_external' => false,
        ]);

        $contactItem = MenuItem::create([
            'menu_id' => $headerMenu->id,
            'title' => 'Contact',
            'url' => '/contact',
            'target' => '_self',
            'icon' => 'mail',
            'sort_order' => 4,
            'is_active' => true,
            'is_external' => false,
        ]);

        // Create Footer Primary Menu
        $footerMenu = Menu::create([
            'name' => 'Footer Primary',
            'location' => 'footer-primary',
            'description' => 'Main footer navigation menu',
            'is_active' => true,
        ]);

        // Create Footer Menu Items
        MenuItem::create([
            'menu_id' => $footerMenu->id,
            'title' => 'Privacy Policy',
            'url' => '/privacy-policy',
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
            'is_external' => false,
        ]);

        MenuItem::create([
            'menu_id' => $footerMenu->id,
            'title' => 'Terms of Service',
            'url' => '/terms-of-service',
            'target' => '_self',
            'sort_order' => 2,
            'is_active' => true,
            'is_external' => false,
        ]);

        MenuItem::create([
            'menu_id' => $footerMenu->id,
            'title' => 'GitHub',
            'url' => 'https://github.com',
            'target' => '_blank',
            'icon' => 'github',
            'sort_order' => 3,
            'is_active' => true,
            'is_external' => true,
        ]);

        // Create Sidebar Menu
        $sidebarMenu = Menu::create([
            'name' => 'Sidebar',
            'location' => 'sidebar',
            'description' => 'Sidebar navigation menu',
            'is_active' => true,
        ]);

        // Create Sidebar Menu Items
        MenuItem::create([
            'menu_id' => $sidebarMenu->id,
            'title' => 'Dashboard',
            'url' => '/dashboard',
            'target' => '_self',
            'icon' => 'layout-grid',
            'sort_order' => 1,
            'is_active' => true,
            'is_external' => false,
        ]);

        MenuItem::create([
            'menu_id' => $sidebarMenu->id,
            'title' => 'Posts',
            'url' => '/posts',
            'target' => '_self',
            'icon' => 'document-text',
            'sort_order' => 2,
            'is_active' => true,
            'is_external' => false,
        ]);

        MenuItem::create([
            'menu_id' => $sidebarMenu->id,
            'title' => 'Categories',
            'url' => '/categories',
            'target' => '_self',
            'icon' => 'folder',
            'sort_order' => 3,
            'is_active' => true,
            'is_external' => false,
        ]);

        MenuItem::create([
            'menu_id' => $sidebarMenu->id,
            'title' => 'Settings',
            'url' => '/settings',
            'target' => '_self',
            'icon' => 'cog',
            'sort_order' => 4,
            'is_active' => true,
            'is_external' => false,
        ]);
    }
}
