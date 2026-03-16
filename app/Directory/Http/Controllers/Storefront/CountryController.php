<?php

declare(strict_types=1);

namespace App\Directory\Http\Controllers\Storefront;

use App\Directory\Contracts\CountryFacade;
use App\Directory\Http\Resources\CountryResource;
use App\Directory\Http\Resources\RegionResource;
use App\Directory\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CountryController extends Controller
{
    public function __construct(
        private readonly CountryFacade $countryFacade,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $storeViewId = $request->query('store_view_id');

        if ($storeViewId !== null) {
            $countries = $this->countryFacade->availableForStoreView((int) $storeViewId);
        } else {
            $countries = $this->countryFacade->listCountries(activeOnly: true);
        }

        return response()->json([
            'data' => CountryResource::collection($countries),
        ]);
    }

    public function regions(Country $country): JsonResponse
    {
        $regions = $this->countryFacade->getRegionsByCountry($country->iso2);

        return response()->json([
            'data' => RegionResource::collection($regions),
        ]);
    }
}
