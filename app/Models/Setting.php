<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group_name',
        'is_public',
        'description',
        'validation_rules',
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'is_public' => 'boolean',
    ];

    /**
     * Scope to get only public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get settings by group
     */
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group_name', $group);
    }

    /**
     * Get the typed value based on the setting type
     */
    public function getTypedValue()
    {
        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'integer' => (int) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    /**
     * Set the value with proper formatting based on type
     */
    public function setValueAttribute($value)
    {
        // Get type from attributes if not set on model
        $type = $this->attributes['type'] ?? $this->type ?? 'string';

        $this->attributes['value'] = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };
    }

    /**
     * Boot method to handle value formatting after model is created/updated
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($setting) {
            // Ensure boolean values are properly formatted
            if ($setting->type === 'boolean') {
                $setting->attributes['value'] = $setting->value ? '1' : '0';
            }
        });
    }
}
