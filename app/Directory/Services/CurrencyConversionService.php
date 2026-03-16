<?php

declare(strict_types=1);

namespace App\Directory\Services;

use App\Directory\Repositories\CurrencyRateRepository;
use App\Directory\Repositories\CurrencyRepository;
use RuntimeException;

class CurrencyConversionService
{
    public function __construct(
        private readonly CurrencyRateRepository $currencyRateRepository,
        private readonly CurrencyRepository $currencyRepository,
    ) {}

    public function convert(float $amount, string $fromCode, string $toCode): float
    {
        if ($fromCode === $toCode) {
            return $amount;
        }

        $rate = $this->currencyRateRepository->getRate($fromCode, $toCode);

        if ($rate !== null) {
            return $amount * $rate->rate;
        }

        $inverseRate = $this->currencyRateRepository->getRate($toCode, $fromCode);

        if ($inverseRate !== null && $inverseRate->rate > 0) {
            return $amount / $inverseRate->rate;
        }

        throw new RuntimeException("No exchange rate found for {$fromCode} to {$toCode}.");
    }

    public function format(float $amount, string $currencyCode): string
    {
        $currency = $this->currencyRepository->findByCode($currencyCode);

        if ($currency === null) {
            throw new RuntimeException("Currency not found: {$currencyCode}.");
        }

        return $currency->symbol.number_format($amount, $currency->decimal_places);
    }
}
