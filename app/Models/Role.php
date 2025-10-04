<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_system',
        'sort_order',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->slug === 'super-admin') {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    public static function createDefaultRoles(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access with all permissions',
                'permissions' => ['*'], // All permissions
                'is_system' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Content Manager',
                'slug' => 'content-manager',
                'description' => 'Manage content, posts, categories, and media',
                'permissions' => [
                    'content.posts.create',
                    'content.posts.edit',
                    'content.posts.delete',
                    'content.posts.view',
                    'content.categories.manage',
                    'content.media.manage',
                    'users.view', // Can view user list but not profiles
                ],
                'is_system' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($roles as $roleData) {
            static::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }
}
