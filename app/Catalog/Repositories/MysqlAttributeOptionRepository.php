<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\AttributeOption;
use Illuminate\Support\Collection;

class MysqlAttributeOptionRepository implements AttributeOptionRepository
{
    public function __construct(
        private readonly AttributeOption $attributeOptionModel,
    ) {}

    public function findById(int $id): ?AttributeOption
    {
        return $this->attributeOptionModel->newQuery()->find($id);
    }

    public function getByAttribute(int $attributeId): Collection
    {
        return $this->attributeOptionModel->newQuery()
            ->where('attribute_id', $attributeId)
            ->orderBy('sort_order')
            ->get();
    }

    public function create(array $data): AttributeOption
    {
        return $this->attributeOptionModel->newQuery()->create($data);
    }

    public function update(AttributeOption $attributeOption, array $data): AttributeOption
    {
        $attributeOption->update($data);

        return $attributeOption;
    }

    public function delete(AttributeOption $attributeOption): bool
    {
        return (bool) $attributeOption->delete();
    }

    public function deleteByAttribute(int $attributeId): int
    {
        return $this->attributeOptionModel->newQuery()
            ->where('attribute_id', $attributeId)
            ->delete();
    }
}
