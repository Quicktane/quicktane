<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\Tax\Http\Requests\StoreTaxZoneRequest;
use Quicktane\Tax\Http\Requests\UpdateTaxZoneRequest;
use Quicktane\Tax\Http\Resources\TaxZoneResource;
use Quicktane\Tax\Models\TaxZone;
use Quicktane\Tax\Repositories\TaxZoneRepository;

class TaxZoneController extends Controller
{
    public function __construct(
        private readonly TaxZoneRepository $taxZoneRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['search', 'is_active']);

        return TaxZoneResource::collection(
            $this->taxZoneRepository->paginate($filters, $perPage),
        );
    }

    public function store(StoreTaxZoneRequest $request): TaxZoneResource
    {
        $validated = $request->validated();
        $rules = $validated['rules'] ?? [];
        unset($validated['rules']);

        $taxZone = $this->taxZoneRepository->create($validated);

        if (! empty($rules)) {
            $this->taxZoneRepository->syncZoneRules($taxZone, $rules);
        }

        $taxZone->load('zoneRules');

        return new TaxZoneResource($taxZone);
    }

    public function show(TaxZone $taxZone): TaxZoneResource
    {
        $taxZone->load('zoneRules');

        return new TaxZoneResource($taxZone);
    }

    public function update(UpdateTaxZoneRequest $request, TaxZone $taxZone): TaxZoneResource
    {
        $validated = $request->validated();
        $rules = $validated['rules'] ?? null;
        unset($validated['rules']);

        $taxZone = $this->taxZoneRepository->update($taxZone, $validated);

        if ($rules !== null) {
            $this->taxZoneRepository->syncZoneRules($taxZone, $rules);
        }

        $taxZone->load('zoneRules');

        return new TaxZoneResource($taxZone);
    }

    public function destroy(TaxZone $taxZone): JsonResponse
    {
        $this->taxZoneRepository->delete($taxZone);

        return response()->json(null, 204);
    }
}
