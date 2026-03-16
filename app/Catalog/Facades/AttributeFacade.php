<?php

declare(strict_types=1);

namespace App\Catalog\Facades;

use App\Catalog\Contracts\AttributeFacade as AttributeFacadeContract;
use App\Catalog\Models\Attribute;
use App\Catalog\Models\AttributeSet;
use App\Catalog\Repositories\AttributeRepository;
use App\Catalog\Repositories\AttributeSetRepository;
use Illuminate\Support\Collection;

class AttributeFacade implements AttributeFacadeContract
{
    public function __construct(
        private readonly AttributeRepository $attributeRepository,
        private readonly AttributeSetRepository $attributeSetRepository,
    ) {}

    public function getAttribute(string $uuid): ?Attribute
    {
        return $this->attributeRepository->findByUuid($uuid);
    }

    public function listAttributes(): Collection
    {
        return $this->attributeRepository->all();
    }

    public function getFilterableAttributes(): Collection
    {
        return $this->attributeRepository->getFilterable();
    }

    public function getAttributeSet(string $uuid): ?AttributeSet
    {
        return $this->attributeSetRepository->findByUuid($uuid);
    }

    public function listAttributeSets(): Collection
    {
        return $this->attributeSetRepository->all();
    }

    public function getAttributesForSet(AttributeSet $attributeSet): Collection
    {
        $attributeSet->load('attributes');

        return $attributeSet->attributes;
    }
}
