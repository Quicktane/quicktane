<?php

declare(strict_types=1);

namespace Quicktane\CMS\Facades;

use Quicktane\CMS\Contracts\UrlFacade as UrlFacadeContract;
use Quicktane\CMS\Enums\EntityType;
use Quicktane\CMS\Models\UrlRewrite;
use Quicktane\CMS\Services\UrlRewriteService;

class UrlFacade implements UrlFacadeContract
{
    public function __construct(
        private readonly UrlRewriteService $urlRewriteService,
    ) {}

    public function resolve(string $requestPath, int $storeViewId = 0): ?UrlRewrite
    {
        return $this->urlRewriteService->resolve($requestPath, $storeViewId);
    }

    public function generateUrl(EntityType $entityType, int $entityId, string $slug, int $storeViewId = 0): UrlRewrite
    {
        return $this->urlRewriteService->generateForEntity($entityType, $entityId, $slug, $storeViewId);
    }

    public function deleteByEntity(EntityType $entityType, int $entityId): void
    {
        $this->urlRewriteService->deleteByEntity($entityType, $entityId);
    }
}
