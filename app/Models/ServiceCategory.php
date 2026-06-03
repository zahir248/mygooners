<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceCategory extends Model
{
    protected $fillable = [
        'name',
        'label',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (ServiceCategory $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function servicesCount(): int
    {
        return Service::where('category', $this->name)->count();
    }

    public static function namesForFilter(): array
    {
        return static::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name')
            ->all();
    }

    public static function optionsForSelect(?string $currentName = null): array
    {
        $options = static::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->pluck('label', 'name')
            ->all();

        if ($currentName && !array_key_exists($currentName, $options)) {
            $category = static::where('name', $currentName)->first();
            $options[$currentName] = $category?->label ?? $currentName;
        }

        return $options;
    }
}
