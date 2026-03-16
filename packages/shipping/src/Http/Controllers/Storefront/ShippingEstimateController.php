<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Http\Controllers\Storefront;

use App\Directory\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Quicktane\Shipping\Contracts\ShippingFacade;
use Quicktane\Shipping\DataTransferObjects\ShippingRateRequest;
use Quicktane\Shipping\Http\Requests\EstimateShippingRequest;
use Quicktane\Shipping\Http\Resources\ShippingRateOptionResource;

class ShippingEstimateController extends Controller
{
    public function __construct(
        private readonly ShippingFacade $shippingFacade,
    ) {}

    public function estimate(EstimateShippingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $country = Country::where('iso2', $validated['country_id'])->first();

        $shippingRateRequest = new ShippingRateRequest(
            items: $validated['items'],
            shippingAddress: [
                'country_id' => $country?->id,
                'region_id' => $validated['region_id'] ?? null,
            ],
            subtotal: (string) $validated['subtotal'],
            totalWeight: isset($validated['total_weight']) ? (string) $validated['total_weight'] : null,
            currencyCode: $validated['currency_code'],
        );

        $rateOptions = $this->shippingFacade->getAvailableRates($shippingRateRequest);

        return response()->json([
            'data' => ShippingRateOptionResource::collection($rateOptions),
        ]);
    }
}
