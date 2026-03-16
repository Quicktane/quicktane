<?php

declare(strict_types=1);

namespace App\Catalog\Models;

use App\Catalog\Enums\AttributeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attribute extends Model
{
    protected $table = 'attributes';

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'type',
        'is_required',
        'is_filterable',
        'is_visible',
        'sort_order',
        'validation_rules',
    ];

    protected function casts(): array
    {
        return [
            'type' => AttributeType::class,
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
            'validation_rules' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function options(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function attributeSets(): BelongsToMany
    {
        return $this->belongsToMany(AttributeSet::class, 'attribute_set_attributes')
            ->withPivot('group_name', 'sort_order');
    }

    protected static function booted(): void
    {
        static::creating(function (Attribute $attribute): void {
            if (empty($attribute->uuid)) {
                $attribute->uuid = (string) Str::uuid();
            }
        });
    }
}
