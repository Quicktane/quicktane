<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Controllers\Storefront;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Quicktane\CMS\Http\Resources\BlockResource;
use Quicktane\CMS\Repositories\BlockRepository;

class BlockController extends Controller
{
    public function __construct(
        private readonly BlockRepository $blockRepository,
    ) {}

    public function show(string $identifier): BlockResource|JsonResponse
    {
        $block = $this->blockRepository->findByIdentifier($identifier);

        if ($block === null || ! $block->is_active) {
            return response()->json(['message' => 'Block not found'], 404);
        }

        return new BlockResource($block);
    }
}
