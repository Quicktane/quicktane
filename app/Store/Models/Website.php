<?php

declare(strict_types=1);

namespace App\Store\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Website extends Model
{
    protected $fillable = [
        'uuid',
        'code',
        'name',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'uuid' => 'string',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Website $website): void {
            if (empty($website->uuid)) {
                $website->uuid = (string) Str::uuid();
            }
        });
    }
}
