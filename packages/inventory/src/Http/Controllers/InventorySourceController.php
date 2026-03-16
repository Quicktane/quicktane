<?php

declare(strict_types=1);

namespace Quicktane\Inventory\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Quicktane\Inventory\Http\Requests\StoreInventorySourceRequest;
use Quicktane\Inventory\Http\Requests\UpdateInventorySourceRequest;
use Quicktane\Inventory\Http\Resources\InventorySourceResource;
use Quicktane\Inventory\Models\InventorySource;
use Quicktane\Inventory\Repositories\InventorySourceRepository;

class InventorySourceController extends Controller
{
    public function __construct(
        private readonly InventorySourceRepository $inventorySourceRepository,
    ) {}

    public function index(): JsonResponse
    {
        $inventorySources = $this->inventorySourceRepository->all();

        return response()->json([
            'data' => InventorySourceResource::collection($inventorySources),
        ]);
    }

    public function store(StoreInventorySourceRequest $request): JsonResponse
    {
        $inventorySource = $this->inventorySourceRepository->create($request->validated());

        return response()->json([
            'data' => new InventorySourceResource($inventorySource),
        ], 201);
    }

    public function show(InventorySource $source): JsonResponse
    {
        return response()->json([
            'data' => new InventorySourceResource($source),
        ]);
    }

    public function update(UpdateInventorySourceRequest $request, InventorySource $source): JsonResponse
    {
        $inventorySource = $this->inventorySourceRepository->update($source, $request->validated());

        return response()->json([
            'data' => new InventorySourceResource($inventorySource),
        ]);
    }

    public function destroy(InventorySource $source): JsonResponse
    {
        $this->inventorySourceRepository->delete($source);

        return response()->json(null, 204);
    }
}
