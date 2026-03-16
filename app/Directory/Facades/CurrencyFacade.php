<?php

declare(strict_types=1);

namespace App\Directory\Facades;

use App\Directory\Contracts\CurrencyFacade as CurrencyFacadeContract;
use App\Directory\Models\CurrencyRate;
use App\Directory\Repositories\CurrencyRateRepository;
use App\Directory\Repositories\CurrencyRepository;
use App\Directory\Services\CurrencyConversionService;
use Illuminate\Support\Collection;

class CurrencyFacade implements CurrencyFacadeContract
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly CurrencyRateRepository $currencyRateRepository,
        private readonly CurrencyConversionService $currencyConversionService,
    ) {}

    public function convert(float $amount, string $fromCode, string $toCode): float
    {
        return $this->currencyConversionService->convert($amount, $fromCode, $toCode);
    }

    public function format(float $amount, string $currencyCode): string
    {
        return $this->currencyConversionService->format($amount, $currencyCode);
    }

    public function listCurrencies(bool $activeOnly = false): Collection
    {
        return $this->currencyRepository->all($activeOnly);
    }

    public function getRate(string $baseCurrencyCode, string $targetCurrencyCode): ?CurrencyRate
    {
        return $this->currencyRateRepository->getRate($baseCurrencyCode, $targetCurrencyCode);
    }

    public function availableForStoreView(int $storeViewId): Collection
    {
        return $this->currencyRepository->getForStoreView($storeViewId);
    }
}
