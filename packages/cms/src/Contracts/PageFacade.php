<?php

declare(strict_types=1);

namespace Quicktane\CMS\Contracts;

use Illuminate\Support\Collection;
use Quicktane\CMS\Models\Page;

interface PageFacade
{
    public function getByIdentifier(string $identifier, int $storeViewId = 0): ?Page;

    public function getActivePagesForStoreView(int $storeViewId): Collection;

    public function getPageContent(string $identifier): ?string;
}
