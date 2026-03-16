<?php

declare(strict_types=1);

namespace Quicktane\CMS\Contracts;

use Quicktane\CMS\Enums\EntityType;
use Quicktane\CMS\Models\UrlRewrite;

interface UrlFacade
{
    public function resolve(string $requestPath, int $storeViewId = 0): ?UrlRewrite;

    public function generateUrl(EntityType $entityType, int $entityId, string $slug, int $storeViewId = 0): UrlRewrite;

    public function deleteByEntity(EntityType $entityType, int $entityId): void;
}
