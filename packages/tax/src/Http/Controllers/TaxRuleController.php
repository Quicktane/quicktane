<?php

declare(strict_types=1);

namespace Quicktane\Tax\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\Tax\Http\Requests\StoreTaxRuleRequest;
use Quicktane\Tax\Http\Requests\UpdateTaxRuleRequest;
use Quicktane\Tax\Http\Resources\TaxRuleResource;
use Quicktane\Tax\Models\TaxRule;
use Quicktane\Tax\Repositories\TaxRuleRepository;

class TaxRuleController extends Controller
{
    public function __construct(
        private readonly TaxRuleRepository $taxRuleRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['product_tax_class_id', 'customer_tax_class_id', 'search', 'is_active']);

        return TaxRuleResource::collection(
            $this->taxRuleRepository->paginate($filters, $perPage),
        );
    }

    public function store(StoreTaxRuleRequest $request): TaxRuleResource
    {
        $taxRule = $this->taxRuleRepository->create($request->validated());
        $taxRule->load(['taxRate', 'productTaxClass', 'customerTaxClass']);

        return new TaxRuleResource($taxRule);
    }

    public function show(TaxRule $taxRule): TaxRuleResource
    {
        $taxRule->load(['taxRate', 'productTaxClass', 'customerTaxClass']);

        return new TaxRuleResource($taxRule);
    }

    public function update(UpdateTaxRuleRequest $request, TaxRule $taxRule): TaxRuleResource
    {
        $taxRule = $this->taxRuleRepository->update($taxRule, $request->validated());
        $taxRule->load(['taxRate', 'productTaxClass', 'customerTaxClass']);

        return new TaxRuleResource($taxRule);
    }

    public function destroy(TaxRule $taxRule): JsonResponse
    {
        $this->taxRuleRepository->delete($taxRule);

        return response()->json(null, 204);
    }
}
