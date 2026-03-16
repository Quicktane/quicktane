<?php

declare(strict_types=1);

namespace Quicktane\CMS\Facades;

use Quicktane\CMS\Contracts\BlockFacade as BlockFacadeContract;
use Quicktane\CMS\Models\Block;
use Quicktane\CMS\Repositories\BlockRepository;

class BlockFacade implements BlockFacadeContract
{
    public function __construct(
        private readonly BlockRepository $blockRepository,
    ) {}

    public function getByIdentifier(string $identifier, int $storeViewId = 0): ?Block
    {
        $block = $this->blockRepository->findByIdentifier($identifier);

        if ($block === null || ! $block->is_active) {
            return null;
        }

        return $block;
    }

    public function renderBlock(string $identifier, int $storeViewId = 0): ?string
    {
        $block = $this->getByIdentifier($identifier, $storeViewId);

        return $block?->content;
    }
}
