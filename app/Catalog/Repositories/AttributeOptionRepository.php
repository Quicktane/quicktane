<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\AttributeOption;
use Illuminate\Support\Collection;

interface AttributeOptionRepository
{
    public function findById(int $id): ?AttributeOption;

    public function getByAttribute(int $attributeId): Collection;

    public function create(array $data): AttributeOption;

    public function update(AttributeOption $attributeOption, array $data): AttributeOption;

    public function delete(AttributeOption $attributeOption): bool;

    public function deleteByAttribute(int $attributeId): int;
}
