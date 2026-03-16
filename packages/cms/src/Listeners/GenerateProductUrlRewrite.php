<?php

declare(strict_types=1);

namespace Quicktane\CMS\Listeners;

use App\Catalog\Events\AfterProductCreate;
use App\Catalog\Events\AfterProductUpdate;
use Quicktane\CMS\Enums\EntityType;
use Quicktane\CMS\Services\UrlRewriteService;

class GenerateProductUrlRewrite
{
    public function __construct(
        private readonly UrlRewriteService $urlRewriteService,
    ) {}

    public function handleCreate(AfterProductCreate $event): void
    {
        $this->urlRewriteService->generateForEntity(
            EntityType::Product,
            $event->product->id,
            $event->product->slug,
        );
    }

    public function handleUpdate(AfterProductUpdate $event): void
    {
        if (! $event->context->has('data') || ! isset($event->context->get('data')['slug'])) {
            return;
        }

        $this->urlRewriteService->generateForEntity(
            EntityType::Product,
            $event->product->id,
            $event->product->slug,
        );
    }
}
