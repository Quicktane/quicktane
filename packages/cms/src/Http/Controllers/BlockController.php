<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\CMS\Http\Requests\StoreBlockRequest;
use Quicktane\CMS\Http\Requests\UpdateBlockRequest;
use Quicktane\CMS\Http\Resources\BlockResource;
use Quicktane\CMS\Models\Block;
use Quicktane\CMS\Repositories\BlockRepository;
use Quicktane\CMS\Services\BlockService;

class BlockController extends Controller
{
    public function __construct(
        private readonly BlockRepository $blockRepository,
        private readonly BlockService $blockService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['search', 'is_active']);

        return BlockResource::collection(
            $this->blockRepository->paginate($filters, $perPage),
        );
    }

    public function store(StoreBlockRequest $request): BlockResource
    {
        $validated = $request->validated();
        $storeViewIds = $validated['store_view_ids'] ?? [0];
        unset($validated['store_view_ids']);

        $block = $this->blockService->createBlock($validated, $storeViewIds);
        $block->load('storeViews');

        return new BlockResource($block);
    }

    public function show(Block $block): BlockResource
    {
        $block->load('storeViews');

        return new BlockResource($block);
    }

    public function update(UpdateBlockRequest $request, Block $block): BlockResource
    {
        $validated = $request->validated();
        $storeViewIds = $validated['store_view_ids'] ?? null;
        unset($validated['store_view_ids']);

        $block = $this->blockService->updateBlock($block, $validated, $storeViewIds);
        $block->load('storeViews');

        return new BlockResource($block);
    }

    public function destroy(Block $block): JsonResponse
    {
        $this->blockService->deleteBlock($block);

        return response()->json(null, 204);
    }
}
