<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\AttributeValue;
use Illuminate\Support\Collection;

interface AttributeValueRepository
{
    public function getByProduct(int $productId): Collection;

    public function findByProductAndAttribute(int $productId, int $attributeId): ?AttributeValue;

    public function upsert(int $productId, int $attributeId, ?string $value): AttributeValue;

    public function deleteByProduct(int $productId): int;
}
