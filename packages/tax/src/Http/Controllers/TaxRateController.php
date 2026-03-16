<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\Tax\Http\Requests\StoreTaxRateRequest;
use Quicktane\Tax\Http\Requests\UpdateTaxRateRequest;
use Quicktane\Tax\Http\Resources\TaxRateResource;
use Quicktane\Tax\Models\TaxRate;
use Quicktane\Tax\Repositories\TaxRateRepository;

class TaxRateController extends Controller
{
    public function __construct(
        private readonly TaxRateRepository $taxRateRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['tax_zone_id', 'search', 'is_active']);

        return TaxRateResource::collection(
            $this->taxRateRepository->paginate($filters, $perPage),
        );
    }

    public function store(StoreTaxRateRequest $request): TaxRateResource
    {
        $taxRate = $this->taxRateRepository->create($request->validated());
        $taxRate->load('zone');

        return new TaxRateResource($taxRate);
    }

    public function show(TaxRate $taxRate): TaxRateResource
    {
        $taxRate->load('zone');

        return new TaxRateResource($taxRate);
    }

    public function update(UpdateTaxRateRequest $request, TaxRate $taxRate): TaxRateResource
    {
        $taxRate = $this->taxRateRepository->update($taxRate, $request->validated());
        $taxRate->load('zone');

        return new TaxRateResource($taxRate);
    }

    public function destroy(TaxRate $taxRate): JsonResponse
    {
        $this->taxRateRepository->delete($taxRate);

        return response()->json(null, 204);
    }
}
