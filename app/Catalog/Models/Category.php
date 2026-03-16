<?php

declare(strict_types=1);

namespace App\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'uuid',
        'parent_id',
        'name',
        'slug',
        'description',
        'path',
        'level',
        'position',
        'is_active',
        'include_in_menu',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'level' => 'integer',
            'position' => 'integer',
            'is_active' => 'boolean',
            'include_in_menu' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories')
            ->withPivot('position');
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category): void {
            if (empty($category->uuid)) {
                $category->uuid = (string) Str::uuid();
            }
        });
    }
}
