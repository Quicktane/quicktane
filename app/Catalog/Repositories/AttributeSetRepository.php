<?php

declare(strict_types=1);

namespace App\Catalog\Repositories;

use App\Catalog\Models\AttributeSet;
use Illuminate\Support\Collection;

interface AttributeSetRepository
{
    public function findById(int $id): ?AttributeSet;

    public function findByUuid(string $uuid): ?AttributeSet;

    public function findByName(string $name): ?AttributeSet;

    public function all(): Collection;

    public function create(array $data): AttributeSet;

    public function update(AttributeSet $attributeSet, array $data): AttributeSet;

    public function delete(AttributeSet $attributeSet): bool;
}
