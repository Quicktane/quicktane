<?php

declare(strict_types=1);

namespace App\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomerSegment extends Model
{
    protected $table = 'customer_segments';

    protected $fillable = [
        'name',
        'description',
        'conditions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'is_active' => 'boolean',
            'uuid' => 'string',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (CustomerSegment $segment): void {
            if (! $segment->uuid) {
                $segment->uuid = (string) Str::uuid();
            }
        });
    }
}
