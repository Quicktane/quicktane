<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\AttributeValue;
use Illuminate\Support\Collection;

class MysqlAttributeValueRepository implements AttributeValueRepository
{
    public function __construct(
        private readonly AttributeValue $attributeValueModel,
    ) {}

    public function getByProduct(int $productId): Collection
    {
        return $this->attributeValueModel->newQuery()
            ->where('product_id', $productId)
            ->get();
    }

    public function findByProductAndAttribute(int $productId, int $attributeId): ?AttributeValue
    {
        return $this->attributeValueModel->newQuery()
            ->where('product_id', $productId)
            ->where('attribute_id', $attributeId)
            ->first();
    }

    public function upsert(int $productId, int $attributeId, ?string $value): AttributeValue
    {
        return $this->attributeValueModel->newQuery()->updateOrCreate(
            [
                'product_id' => $productId,
                'attribute_id' => $attributeId,
            ],
            [
                'value' => $value,
            ],
        );
    }

    public function deleteByProduct(int $productId): int
    {
        return $this->attributeValueModel->newQuery()
            ->where('product_id', $productId)
            ->delete();
    }
}
