<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\Tax\Http\Requests\StoreTaxClassRequest;
use Quicktane\Tax\Http\Requests\UpdateTaxClassRequest;
use Quicktane\Tax\Http\Resources\TaxClassResource;
use Quicktane\Tax\Models\TaxClass;
use Quicktane\Tax\Repositories\TaxClassRepository;

class TaxClassController extends Controller
{
    public function __construct(
        private readonly TaxClassRepository $taxClassRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['type', 'search']);

        return TaxClassResource::collection(
            $this->taxClassRepository->paginate($filters, $perPage),
        );
    }

    public function store(StoreTaxClassRequest $request): TaxClassResource
    {
        $taxClass = $this->taxClassRepository->create($request->validated());

        return new TaxClassResource($taxClass);
    }

    public function show(TaxClass $taxClass): TaxClassResource
    {
        return new TaxClassResource($taxClass);
    }

    public function update(UpdateTaxClassRequest $request, TaxClass $taxClass): TaxClassResource
    {
        $taxClass = $this->taxClassRepository->update($taxClass, $request->validated());

        return new TaxClassResource($taxClass);
    }

    public function destroy(TaxClass $taxClass): JsonResponse
    {
        $this->taxClassRepository->delete($taxClass);

        return response()->json(null, 204);
    }
}
