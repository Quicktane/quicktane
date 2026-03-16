<?php

declare(strict_types=1);

namespace App\Catalog\Contracts;

use App\Catalog\Models\Category;
use Illuminate\Support\Collection;

interface CategoryFacade
{
    public function getCategory(string $uuid): ?Category;

    public function getCategoryTree(): Collection;

    public function getRootCategories(): Collection;

    public function getCategoryWithChildren(string $uuid): ?Category;
}
