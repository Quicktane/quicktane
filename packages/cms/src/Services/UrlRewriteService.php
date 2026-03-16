<?php

declare(strict_types=1);

namespace Quicktane\CMS\Services;

use Quicktane\CMS\Enums\EntityType;
use Quicktane\CMS\Enums\RedirectType;
use Quicktane\CMS\Models\UrlRewrite;
use Quicktane\CMS\Repositories\UrlRewriteRepository;

class UrlRewriteService
{
    public function __construct(
        private readonly UrlRewriteRepository $urlRewriteRepository,
    ) {}

    public function generateForEntity(
        EntityType $entityType,
        int $entityId,
        string $slug,
        int $storeViewId = 0,
    ): UrlRewrite {
        $targetPath = $this->buildTargetPath($entityType, $slug);
        $requestPath = $slug;

        $existing = $this->urlRewriteRepository->findByEntity($entityType, $entityId)
            ->where('store_view_id', $storeViewId)
            ->where('redirect_type', null)
            ->first();

        if ($existing !== null) {
            if ($existing->request_path === $requestPath) {
                return $existing;
            }

            $this->urlRewriteRepository->update($existing, [
                'redirect_type' => RedirectType::Permanent->value,
                'target_path' => $requestPath,
            ]);

            return $this->urlRewriteRepository->create([
                'entity_type' => $entityType->value,
                'entity_id' => $entityId,
                'request_path' => $requestPath,
                'target_path' => $targetPath,
                'redirect_type' => null,
                'store_view_id' => $storeViewId,
            ]);
        }

        return $this->urlRewriteRepository->create([
            'entity_type' => $entityType->value,
            'entity_id' => $entityId,
            'request_path' => $requestPath,
            'target_path' => $targetPath,
            'redirect_type' => null,
            'store_view_id' => $storeViewId,
        ]);
    }

    public function deleteByEntity(EntityType $entityType, int $entityId): void
    {
        $this->urlRewriteRepository->deleteByEntity($entityType, $entityId);
    }

    public function resolve(string $requestPath, int $storeViewId = 0): ?UrlRewrite
    {
        return $this->urlRewriteRepository->resolveByRequestPath($requestPath, $storeViewId);
    }

    private function buildTargetPath(EntityType $entityType, string $slug): string
    {
        return match ($entityType) {
            EntityType::Product => "api/v1/catalog/products/{$slug}",
            EntityType::Category => "api/v1/catalog/categories/{$slug}",
            EntityType::CmsPage => "api/v1/cms/pages/{$slug}",
            EntityType::Custom => $slug,
        };
    }
}
