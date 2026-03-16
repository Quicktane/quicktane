<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\Attribute;
use Illuminate\Support\Collection;

class MysqlAttributeRepository implements AttributeRepository
{
    public function __construct(
        private readonly Attribute $attributeModel,
    ) {}

    public function findById(int $id): ?Attribute
    {
        return $this->attributeModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Attribute
    {
        return $this->attributeModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCode(string $code): ?Attribute
    {
        return $this->attributeModel->newQuery()->where('code', $code)->first();
    }

    public function all(): Collection
    {
        return $this->attributeModel->newQuery()->orderBy('sort_order')->get();
    }

    public function getFilterable(): Collection
    {
        return $this->attributeModel->newQuery()
            ->where('is_filterable', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function create(array $data): Attribute
    {
        return $this->attributeModel->newQuery()->create($data);
    }

    public function update(Attribute $attribute, array $data): Attribute
    {
        $attribute->update($data);

        return $attribute;
    }

    public function delete(Attribute $attribute): bool
    {
        return (bool) $attribute->delete();
    }
}
