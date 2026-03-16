<?php

declare(strict_types=1);

namespace Quicktane\CMS\Services;

use Quicktane\CMS\Events\AfterBlockCreate;
use Quicktane\CMS\Events\AfterBlockDelete;
use Quicktane\CMS\Events\AfterBlockUpdate;
use Quicktane\CMS\Events\BeforeBlockCreate;
use Quicktane\CMS\Events\BeforeBlockDelete;
use Quicktane\CMS\Events\BeforeBlockUpdate;
use Quicktane\CMS\Models\Block;
use Quicktane\CMS\Repositories\BlockRepository;
use Quicktane\Core\Events\EventDispatcher;
use Quicktane\Core\Events\OperationContext;
use Quicktane\Core\Trace\OperationTracer;

class BlockService
{
    public function __construct(
        private readonly BlockRepository $blockRepository,
        private readonly OperationTracer $operationTracer,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int>  $storeViewIds
     */
    public function createBlock(array $data, array $storeViewIds = [0]): Block
    {
        return $this->operationTracer->execute('block.create', function () use ($data, $storeViewIds): Block {
            $context = new OperationContext;
            $context->set('data', $data);
            $context->set('store_view_ids', $storeViewIds);

            $this->eventDispatcher->dispatch(new BeforeBlockCreate($context));

            $block = $this->blockRepository->create($data);

            $block->storeViews()->sync($storeViewIds);

            $this->eventDispatcher->dispatch(new AfterBlockCreate($block, $context));

            return $block;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int>|null  $storeViewIds
     */
    public function updateBlock(Block $block, array $data, ?array $storeViewIds = null): Block
    {
        return $this->operationTracer->execute('block.update', function () use ($block, $data, $storeViewIds): Block {
            $context = new OperationContext;
            $context->set('data', $data);
            $context->set('block_id', $block->id);

            $this->eventDispatcher->dispatch(new BeforeBlockUpdate($block, $context));

            $block = $this->blockRepository->update($block, $data);

            if ($storeViewIds !== null) {
                $block->storeViews()->sync($storeViewIds);
            }

            $this->eventDispatcher->dispatch(new AfterBlockUpdate($block, $context));

            return $block;
        });
    }

    public function deleteBlock(Block $block): bool
    {
        return $this->operationTracer->execute('block.delete', function () use ($block): bool {
            $context = new OperationContext;
            $context->set('block_id', $block->id);
            $context->set('block_identifier', $block->identifier);

            $this->eventDispatcher->dispatch(new BeforeBlockDelete($block, $context));

            $result = $this->blockRepository->delete($block);

            $this->eventDispatcher->dispatch(new AfterBlockDelete($block, $context));

            return $result;
        });
    }
}
