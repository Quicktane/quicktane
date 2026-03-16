<?php

declare(strict_types=1);

namespace Quicktane\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Quicktane\CMS\Enums\EntityType;
use Quicktane\CMS\Enums\RedirectType;

class UrlRewrite extends Model
{
    protected $table = 'url_rewrites';

    protected $fillable = [
        'uuid',
        'entity_type',
        'entity_id',
        'request_path',
        'target_path',
        'redirect_type',
        'store_view_id',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'entity_type' => EntityType::class,
            'entity_id' => 'integer',
            'redirect_type' => RedirectType::class,
            'store_view_id' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (UrlRewrite $urlRewrite): void {
            if (empty($urlRewrite->uuid)) {
                $urlRewrite->uuid = (string) Str::uuid();
            }
        });
    }

    public function isRedirect(): bool
    {
        return $this->redirect_type !== null;
    }

    public function isInternal(): bool
    {
        return $this->redirect_type === null;
    }
}
