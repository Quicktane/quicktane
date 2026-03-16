<?php

declare(strict_types=1);

namespace App\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class AttributeSet extends Model
{
    protected $table = 'attribute_sets';

    protected $fillable = [
        'uuid',
        'name',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'attribute_set_attributes')
            ->withPivot('group_name', 'sort_order');
    }

    protected static function booted(): void
    {
        static::creating(function (AttributeSet $attributeSet): void {
            if (empty($attributeSet->uuid)) {
                $attributeSet->uuid = (string) Str::uuid();
            }
        });
    }
}
