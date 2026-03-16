<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\AttributeSet;
use Illuminate\Support\Collection;

class MysqlAttributeSetRepository implements AttributeSetRepository
{
    public function __construct(
        private readonly AttributeSet $attributeSetModel,
    ) {}

    public function findById(int $id): ?AttributeSet
    {
        return $this->attributeSetModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?AttributeSet
    {
        return $this->attributeSetModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByName(string $name): ?AttributeSet
    {
        return $this->attributeSetModel->newQuery()->where('name', $name)->first();
    }

    public function all(): Collection
    {
        return $this->attributeSetModel->newQuery()->orderBy('sort_order')->get();
    }

    public function create(array $data): AttributeSet
    {
        return $this->attributeSetModel->newQuery()->create($data);
    }

    public function update(AttributeSet $attributeSet, array $data): AttributeSet
    {
        $attributeSet->update($data);

        return $attributeSet;
    }

    public function delete(AttributeSet $attributeSet): bool
    {
        return (bool) $attributeSet->delete();
    }
}
