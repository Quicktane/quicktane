<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\Attribute;
use Illuminate\Support\Collection;

interface AttributeRepository
{
    public function findById(int $id): ?Attribute;

    public function findByUuid(string $uuid): ?Attribute;

    public function findByCode(string $code): ?Attribute;

    public function all(): Collection;

    public function getFilterable(): Collection;

    public function create(array $data): Attribute;

    public function update(Attribute $attribute, array $data): Attribute;

    public function delete(Attribute $attribute): bool;
}
