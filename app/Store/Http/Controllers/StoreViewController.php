<?php

declare(strict_types=1);

namespace App\Store\Http\Controllers;

use App\Store\Http\Requests\StoreStoreViewRequest;
use App\Store\Http\Requests\UpdateStoreViewRequest;
use App\Store\Http\Resources\StoreViewResource;
use App\Store\Models\StoreView;
use App\Store\Repositories\StoreViewRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class StoreViewController extends Controller
{
    public function __construct(
        private readonly StoreViewRepository $storeViewRepository,
    ) {}

    public function index(): JsonResponse
    {
        $storeViews = $this->storeViewRepository->all();

        return response()->json([
            'data' => StoreViewResource::collection($storeViews),
        ]);
    }

    public function store(StoreStoreViewRequest $request): JsonResponse
    {
        $storeView = $this->storeViewRepository->create($request->validated());

        return response()->json([
            'data' => new StoreViewResource($storeView),
        ], 201);
    }

    public function show(StoreView $storeView): JsonResponse
    {
        return response()->json([
            'data' => new StoreViewResource($storeView->load('store')),
        ]);
    }

    public function update(UpdateStoreViewRequest $request, StoreView $storeView): JsonResponse
    {
        $storeView = $this->storeViewRepository->update($storeView, $request->validated());

        return response()->json([
            'data' => new StoreViewResource($storeView),
        ]);
    }

    public function destroy(StoreView $storeView): JsonResponse
    {
        $this->storeViewRepository->delete($storeView);

        return response()->json(null, 204);
    }
}
