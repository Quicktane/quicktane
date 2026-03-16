<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Quicktane\Inventory\Http\Requests\AdjustStockRequest;
use Quicktane\Inventory\Http\Requests\UpdateStockItemRequest;
use Quicktane\Inventory\Http\Resources\StockItemResource;
use Quicktane\Inventory\Repositories\StockItemRepository;
use Quicktane\Inventory\Services\StockService;

class StockItemController extends Controller
{
    public function __construct(
        private readonly StockItemRepository $stockItemRepository,
        private readonly StockService $stockService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['source_id', 'product_id', 'is_in_stock']);
        $perPage = (int) $request->input('per_page', 15);

        $stockItems = $this->stockItemRepository->paginate($perPage, $filters);

        return response()->json(StockItemResource::collection($stockItems)->response()->getData(true));
    }

    public function update(UpdateStockItemRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $stockItem = $this->stockService->setStock(
            $validated['product_id'],
            $validated['source_id'],
            $validated['quantity'],
            $validated['reason'],
            $request->user()?->id,
        );

        if (isset($validated['notify_quantity'])) {
            $this->stockItemRepository->upsert(
                $validated['product_id'],
                $validated['source_id'],
                ['notify_quantity' => $validated['notify_quantity']],
            );
            $stockItem->refresh();
        }

        return response()->json([
            'data' => new StockItemResource($stockItem),
        ]);
    }

    public function adjust(AdjustStockRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $success = $this->stockService->adjustStock(
            $validated['product_id'],
            $validated['source_id'],
            $validated['quantity_change'],
            $validated['reason'],
            $request->user()?->id,
        );

        if (! $success) {
            return response()->json([
                'message' => 'Insufficient stock for the requested adjustment.',
            ], 422);
        }

        $stockItem = $this->stockItemRepository->findByProductAndSource(
            $validated['product_id'],
            $validated['source_id'],
        );

        return response()->json([
            'data' => $stockItem !== null ? new StockItemResource($stockItem) : null,
        ]);
    }

    public function lowStock(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);

        $stockItems = $this->stockItemRepository->getLowStock($perPage);

        return response()->json(StockItemResource::collection($stockItems)->response()->getData(true));
    }
}
