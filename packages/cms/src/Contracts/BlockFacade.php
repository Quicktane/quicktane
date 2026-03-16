<?php

declare(strict_types=1);

namespace Quicktane\CMS\Contracts;

use Quicktane\CMS\Models\Block;

interface BlockFacade
{
    public function getByIdentifier(string $identifier, int $storeViewId = 0): ?Block;

    public function renderBlock(string $identifier, int $storeViewId = 0): ?string;
}
