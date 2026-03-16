<?php

declare(strict_types=1);

namespace App\Directory\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'decimal_places',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'decimal_places' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
