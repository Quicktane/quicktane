<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Quicktane\Shipping\Http\Requests\StoreShippingZoneRequest;
use Quicktane\Shipping\Http\Requests\UpdateShippingZoneRequest;
use Quicktane\Shipping\Http\Resources\ShippingZoneResource;
use Quicktane\Shipping\Models\ShippingZone;
use Quicktane\Shipping\Repositories\ShippingZoneRepository;

class ShippingZoneController extends Controller
{
    public function __construct(
        private readonly ShippingZoneRepository $shippingZoneRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $shippingZones = $this->shippingZoneRepository->paginate(
            (int) $request->input('per_page', 20),
            $request->only(['is_active']),
        );

        return response()->json(
            ShippingZoneResource::collection($shippingZones)->response()->getData(true),
        );
    }

    public function store(StoreShippingZoneRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $countriesData = $validated['countries'] ?? [];
        unset($validated['countries']);

        $shippingZone = $this->shippingZoneRepository->create($validated);

        if (count($countriesData) > 0) {
            $shippingZone->countries()->createMany($countriesData);
        }

        $shippingZone->load('countries');

        return response()->json([
            'data' => new ShippingZoneResource($shippingZone),
        ], 201);
    }

    public function show(ShippingZone $zone): JsonResponse
    {
        $zone->load('countries');

        return response()->json([
            'data' => new ShippingZoneResource($zone),
        ]);
    }

    public function update(UpdateShippingZoneRequest $request, ShippingZone $zone): JsonResponse
    {
        $validated = $request->validated();
        $countriesData = $validated['countries'] ?? null;
        unset($validated['countries']);

        $shippingZone = $this->shippingZoneRepository->update($zone, $validated);

        if ($countriesData !== null) {
            $shippingZone->countries()->delete();
            $shippingZone->countries()->createMany($countriesData);
        }

        $shippingZone->load('countries');

        return response()->json([
            'data' => new ShippingZoneResource($shippingZone),
        ]);
    }

    public function destroy(ShippingZone $zone): JsonResponse
    {
        $this->shippingZoneRepository->delete($zone);

        return response()->json(null, 204);
    }
}
