<?php

declare(strict_types=1);

namespace Quicktane\CMS\Facades;

use Illuminate\Support\Collection;
use Quicktane\CMS\Contracts\PageFacade as PageFacadeContract;
use Quicktane\CMS\Models\Page;
use Quicktane\CMS\Repositories\PageRepository;

class PageFacade implements PageFacadeContract
{
    public function __construct(
        private readonly PageRepository $pageRepository,
    ) {}

    public function getByIdentifier(string $identifier, int $storeViewId = 0): ?Page
    {
        $page = $this->pageRepository->findByIdentifier($identifier);

        if ($page === null || ! $page->is_active) {
            return null;
        }

        return $page;
    }

    public function getActivePagesForStoreView(int $storeViewId): Collection
    {
        return $this->pageRepository->findActiveForStoreView($storeViewId);
    }

    public function getPageContent(string $identifier): ?string
    {
        $page = $this->pageRepository->findByIdentifier($identifier);

        if ($page === null || ! $page->is_active) {
            return null;
        }

        return $page->content;
    }
}
