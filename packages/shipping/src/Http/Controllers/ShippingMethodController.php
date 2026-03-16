<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Quicktane\Shipping\Http\Requests\StoreShippingMethodRequest;
use Quicktane\Shipping\Http\Requests\UpdateShippingMethodRequest;
use Quicktane\Shipping\Http\Resources\ShippingMethodResource;
use Quicktane\Shipping\Models\ShippingMethod;
use Quicktane\Shipping\Repositories\ShippingMethodRepository;

class ShippingMethodController extends Controller
{
    public function __construct(
        private readonly ShippingMethodRepository $shippingMethodRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $shippingMethods = $this->shippingMethodRepository->paginate(
            (int) $request->input('per_page', 20),
            $request->only(['is_active', 'carrier_code']),
        );

        return response()->json(
            ShippingMethodResource::collection($shippingMethods)->response()->getData(true),
        );
    }

    public function store(StoreShippingMethodRequest $request): JsonResponse
    {
        $shippingMethod = $this->shippingMethodRepository->create($request->validated());

        return response()->json([
            'data' => new ShippingMethodResource($shippingMethod),
        ], 201);
    }

    public function show(ShippingMethod $method): JsonResponse
    {
        return response()->json([
            'data' => new ShippingMethodResource($method),
        ]);
    }

    public function update(UpdateShippingMethodRequest $request, ShippingMethod $method): JsonResponse
    {
        $shippingMethod = $this->shippingMethodRepository->update($method, $request->validated());

        return response()->json([
            'data' => new ShippingMethodResource($shippingMethod),
        ]);
    }

    public function destroy(ShippingMethod $method): JsonResponse
    {
        $this->shippingMethodRepository->delete($method);

        return response()->json(null, 204);
    }
}
