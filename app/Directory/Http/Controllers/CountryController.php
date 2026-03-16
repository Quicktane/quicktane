<?php

declare(strict_types=1);

namespace App\Directory\Http\Controllers;

use App\Directory\Http\Requests\UpdateCountryRequest;
use App\Directory\Http\Resources\CountryResource;
use App\Directory\Http\Resources\RegionResource;
use App\Directory\Models\Country;
use App\Directory\Models\Region;
use App\Directory\Repositories\CountryRepository;
use App\Directory\Repositories\RegionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CountryController extends Controller
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly RegionRepository $regionRepository,
    ) {}

    public function index(): JsonResponse
    {
        $countries = $this->countryRepository->all();

        return response()->json([
            'data' => CountryResource::collection($countries),
        ]);
    }

    public function show(Country $country): JsonResponse
    {
        return response()->json([
            'data' => new CountryResource($country->load('regions')),
        ]);
    }

    public function update(UpdateCountryRequest $request, Country $country): JsonResponse
    {
        $country = $this->countryRepository->update($country, $request->validated());

        return response()->json([
            'data' => new CountryResource($country),
        ]);
    }

    public function regions(Country $country): JsonResponse
    {
        $regions = $this->regionRepository->getByCountry($country->id);

        return response()->json([
            'data' => RegionResource::collection($regions),
        ]);
    }

    public function updateRegion(Request $request, Country $country, Region $region): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer'],
        ]);

        $region = $this->regionRepository->update($region, $validated);

        return response()->json([
            'data' => new RegionResource($region),
        ]);
    }
}
