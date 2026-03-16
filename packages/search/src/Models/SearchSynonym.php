<?php

declare(strict_types=1);

namespace Quicktane\Search\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SearchSynonym extends Model
{
    protected $table = 'search_synonyms';

    protected $fillable = [
        'uuid',
        'term',
        'synonyms',
        'store_view_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'synonyms' => 'array',
            'is_active' => 'boolean',
            'store_view_id' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (SearchSynonym $searchSynonym): void {
            if (empty($searchSynonym->uuid)) {
                $searchSynonym->uuid = (string) Str::uuid();
            }
        });
    }
}
