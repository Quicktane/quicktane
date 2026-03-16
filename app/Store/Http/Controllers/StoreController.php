<?php

declare(strict_types=1);

namespace App\Store\Http\Controllers;

use App\Store\Http\Requests\StoreStoreRequest;
use App\Store\Http\Requests\UpdateStoreRequest;
use App\Store\Http\Resources\StoreResource;
use App\Store\Models\Store;
use App\Store\Repositories\StoreRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class StoreController extends Controller
{
    public function __construct(
        private readonly StoreRepository $storeRepository,
    ) {}

    public function index(): JsonResponse
    {
        $stores = $this->storeRepository->all();

        return response()->json([
            'data' => StoreResource::collection($stores),
        ]);
    }

    public function store(StoreStoreRequest $request): JsonResponse
    {
        $store = $this->storeRepository->create($request->validated());

        return response()->json([
            'data' => new StoreResource($store),
        ], 201);
    }

    public function show(Store $store): JsonResponse
    {
        return response()->json([
            'data' => new StoreResource($store->load(['website', 'storeViews'])),
        ]);
    }

    public function update(UpdateStoreRequest $request, Store $store): JsonResponse
    {
        $store = $this->storeRepository->update($store, $request->validated());

        return response()->json([
            'data' => new StoreResource($store),
        ]);
    }

    public function destroy(Store $store): JsonResponse
    {
        $this->storeRepository->delete($store);

        return response()->json(null, 204);
    }
}
