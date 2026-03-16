<?php

declare(strict_types=1);

namespace App\Directory\Http\Controllers;

use App\Directory\Http\Requests\UpdateCurrencyRequest;
use App\Directory\Http\Resources\CurrencyResource;
use App\Directory\Models\Currency;
use App\Directory\Repositories\CurrencyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
    ) {}

    public function index(): JsonResponse
    {
        $currencies = $this->currencyRepository->all();

        return response()->json([
            'data' => CurrencyResource::collection($currencies),
        ]);
    }

    public function show(Currency $currency): JsonResponse
    {
        return response()->json([
            'data' => new CurrencyResource($currency),
        ]);
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency): JsonResponse
    {
        $currency = $this->currencyRepository->update($currency, $request->validated());

        return response()->json([
            'data' => new CurrencyResource($currency),
        ]);
    }
}
