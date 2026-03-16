<?php

declare(strict_types=1);

namespace Quicktane\CMS\Services;

use Quicktane\CMS\Enums\EntityType;
use Quicktane\CMS\Events\AfterPageCreate;
use Quicktane\CMS\Events\AfterPageDelete;
use Quicktane\CMS\Events\AfterPageUpdate;
use Quicktane\CMS\Events\BeforePageCreate;
use Quicktane\CMS\Events\BeforePageDelete;
use Quicktane\CMS\Events\BeforePageUpdate;
use Quicktane\CMS\Models\Page;
use Quicktane\CMS\Repositories\PageRepository;
use Quicktane\Core\Events\EventDispatcher;
use Quicktane\Core\Events\OperationContext;
use Quicktane\Core\Trace\OperationTracer;

class PageService
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly UrlRewriteService $urlRewriteService,
        private readonly OperationTracer $operationTracer,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int>  $storeViewIds
     */
    public function createPage(array $data, array $storeViewIds = [0]): Page
    {
        return $this->operationTracer->execute('page.create', function () use ($data, $storeViewIds): Page {
            $context = new OperationContext;
            $context->set('data', $data);
            $context->set('store_view_ids', $storeViewIds);

            $this->eventDispatcher->dispatch(new BeforePageCreate($context));

            $page = $this->pageRepository->create($data);

            $page->storeViews()->sync($storeViewIds);

            $this->urlRewriteService->generateForEntity(
                EntityType::CmsPage,
                $page->id,
                $page->identifier,
            );

            $this->eventDispatcher->dispatch(new AfterPageCreate($page, $context));

            return $page;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int>|null  $storeViewIds
     */
    public function updatePage(Page $page, array $data, ?array $storeViewIds = null): Page
    {
        return $this->operationTracer->execute('page.update', function () use ($page, $data, $storeViewIds): Page {
            $context = new OperationContext;
            $context->set('data', $data);
            $context->set('page_id', $page->id);

            $oldIdentifier = $page->identifier;

            $this->eventDispatcher->dispatch(new BeforePageUpdate($page, $context));

            $page = $this->pageRepository->update($page, $data);

            if ($storeViewIds !== null) {
                $page->storeViews()->sync($storeViewIds);
            }

            if (isset($data['identifier']) && $data['identifier'] !== $oldIdentifier) {
                $this->urlRewriteService->generateForEntity(
                    EntityType::CmsPage,
                    $page->id,
                    $page->identifier,
                );
            }

            $this->eventDispatcher->dispatch(new AfterPageUpdate($page, $context));

            return $page;
        });
    }

    public function deletePage(Page $page): bool
    {
        return $this->operationTracer->execute('page.delete', function () use ($page): bool {
            $context = new OperationContext;
            $context->set('page_id', $page->id);
            $context->set('page_identifier', $page->identifier);

            $this->eventDispatcher->dispatch(new BeforePageDelete($page, $context));

            $this->urlRewriteService->deleteByEntity(EntityType::CmsPage, $page->id);

            $result = $this->pageRepository->delete($page);

            $this->eventDispatcher->dispatch(new AfterPageDelete($page, $context));

            return $result;
        });
    }
}
