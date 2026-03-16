<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Quicktane\Shipping\Http\Requests\StoreShippingRateRequest;
use Quicktane\Shipping\Http\Requests\UpdateShippingRateRequest;
use Quicktane\Shipping\Http\Resources\ShippingRateResource;
use Quicktane\Shipping\Models\ShippingRate;
use Quicktane\Shipping\Repositories\ShippingRateRepository;

class ShippingRateController extends Controller
{
    public function __construct(
        private readonly ShippingRateRepository $shippingRateRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $shippingRates = $this->shippingRateRepository->paginate(
            (int) $request->input('per_page', 20),
            $request->only(['shipping_method_id', 'shipping_zone_id', 'is_active']),
        );

        return response()->json(
            ShippingRateResource::collection($shippingRates)->response()->getData(true),
        );
    }

    public function store(StoreShippingRateRequest $request): JsonResponse
    {
        $shippingRate = $this->shippingRateRepository->create($request->validated());

        return response()->json([
            'data' => new ShippingRateResource($shippingRate),
        ], 201);
    }

    public function show(ShippingRate $rate): JsonResponse
    {
        $rate->load(['method', 'zone']);

        return response()->json([
            'data' => new ShippingRateResource($rate),
        ]);
    }

    public function update(UpdateShippingRateRequest $request, ShippingRate $rate): JsonResponse
    {
        $shippingRate = $this->shippingRateRepository->update($rate, $request->validated());

        return response()->json([
            'data' => new ShippingRateResource($shippingRate),
        ]);
    }

    public function destroy(ShippingRate $rate): JsonResponse
    {
        $this->shippingRateRepository->delete($rate);

        return response()->json(null, 204);
    }
}
