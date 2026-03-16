<?php

declare(strict_types=1);

namespace Quicktane\CMS\Models;

use App\Store\Models\StoreView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Quicktane\CMS\Enums\PageLayout;

class Page extends Model
{
    use SoftDeletes;

    protected $table = 'cms_pages';

    protected $fillable = [
        'uuid',
        'identifier',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
        'sort_order',
        'layout',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'layout' => PageLayout::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (Page $page): void {
            if (empty($page->uuid)) {
                $page->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * @return BelongsToMany<StoreView, $this>
     */
    public function storeViews(): BelongsToMany
    {
        return $this->belongsToMany(StoreView::class, 'cms_page_store_views', 'page_id', 'store_view_id');
    }

    /**
     * @return HasMany<UrlRewrite, $this>
     */
    public function urlRewrites(): HasMany
    {
        return $this->hasMany(UrlRewrite::class, 'entity_id')
            ->where('entity_type', 'cms_page');
    }
}
