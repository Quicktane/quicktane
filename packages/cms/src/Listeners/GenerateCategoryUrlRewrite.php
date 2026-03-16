<?php

declare(strict_types=1);

namespace Quicktane\CMS\Listeners;

use App\Catalog\Events\AfterCategoryCreate;
use App\Catalog\Events\AfterCategoryUpdate;
use Quicktane\CMS\Enums\EntityType;
use Quicktane\CMS\Services\UrlRewriteService;

class GenerateCategoryUrlRewrite
{
    public function __construct(
        private readonly UrlRewriteService $urlRewriteService,
    ) {}

    public function handleCreate(AfterCategoryCreate $event): void
    {
        $this->urlRewriteService->generateForEntity(
            EntityType::Category,
            $event->category->id,
            $event->category->slug,
        );
    }

    public function handleUpdate(AfterCategoryUpdate $event): void
    {
        if (! $event->context->has('data') || ! isset($event->context->get('data')['slug'])) {
            return;
        }

        $this->urlRewriteService->generateForEntity(
            EntityType::Category,
            $event->category->id,
            $event->category->slug,
        );
    }
}
