<?php

declare(strict_types=1);

namespace App\Catalog\Models;

use App\Catalog\Enums\ProductType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Quicktane\Media\Models\MediaFile;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'uuid',
        'type',
        'attribute_set_id',
        'sku',
        'name',
        'slug',
        'description',
        'short_description',
        'base_price',
        'special_price',
        'special_price_from',
        'special_price_to',
        'cost',
        'weight',
        'is_active',
        'tax_class_id',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
            'attribute_set_id' => 'integer',
            'base_price' => 'decimal:4',
            'special_price' => 'decimal:4',
            'cost' => 'decimal:4',
            'weight' => 'decimal:4',
            'is_active' => 'boolean',
            'tax_class_id' => 'integer',
            'special_price_from' => 'datetime',
            'special_price_to' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function attributeSet(): BelongsTo
    {
        return $this->belongsTo(AttributeSet::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories')
            ->withPivot('position');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(MediaFile::class, 'product_media')
            ->withPivot('position', 'label', 'is_main');
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
        });
    }
}
