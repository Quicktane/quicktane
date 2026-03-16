<?php

declare(strict_types=1);

namespace App\Directory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'iso2',
        'iso3',
        'name',
        'numeric_code',
        'phone_code',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'iso2';
    }

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }
}
