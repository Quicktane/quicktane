<?php

declare(strict_types=1);

namespace Quicktane\CMS\Models;

use App\Store\Models\StoreView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Block extends Model
{
    use SoftDeletes;

    protected $table = 'cms_blocks';

    protected $fillable = [
        'uuid',
        'identifier',
        'title',
        'content',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (Block $block): void {
            if (empty($block->uuid)) {
                $block->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * @return BelongsToMany<StoreView, $this>
     */
    public function storeViews(): BelongsToMany
    {
        return $this->belongsToMany(StoreView::class, 'cms_block_store_views', 'block_id', 'store_view_id');
    }
}
