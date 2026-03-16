<?php

declare(strict_types=1);

namespace App\Directory\Http\Controllers;

use App\Directory\Http\Requests\StoreCurrencyRateRequest;
use App\Directory\Http\Resources\CurrencyRateResource;
use App\Directory\Repositories\CurrencyRateRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CurrencyRateController extends Controller
{
    public function __construct(
        private readonly CurrencyRateRepository $currencyRateRepository,
    ) {}

    public function index(): JsonResponse
    {
        $rates = $this->currencyRateRepository->all();

        return response()->json([
            'data' => CurrencyRateResource::collection($rates),
        ]);
    }

    public function update(StoreCurrencyRateRequest $request): JsonResponse
    {
        $rate = $this->currencyRateRepository->setRate(
            $request->validated('base_currency_code'),
            $request->validated('target_currency_code'),
            (float) $request->validated('rate'),
        );

        return response()->json([
            'data' => new CurrencyRateResource($rate),
        ]);
    }
}
