<?php

declare(strict_types=1);

namespace App\Catalog\Contracts;

use App\Catalog\Models\Attribute;
use App\Catalog\Models\AttributeSet;
use Illuminate\Support\Collection;

interface AttributeFacade
{
    public function getAttribute(string $uuid): ?Attribute;

    public function listAttributes(): Collection;

    public function getFilterableAttributes(): Collection;

    public function getAttributeSet(string $uuid): ?AttributeSet;

    public function listAttributeSets(): Collection;

    public function getAttributesForSet(AttributeSet $attributeSet): Collection;
}
