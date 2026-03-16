<?php

declare(strict_types=1);

namespace App\Directory\Repositories;

use App\Directory\Models\CurrencyRate;
use Illuminate\Support\Collection;

class MysqlCurrencyRateRepository implements CurrencyRateRepository
{
    public function all(): Collection
    {
        return CurrencyRate::all();
    }

    public function getRate(string $baseCurrencyCode, string $targetCurrencyCode): ?CurrencyRate
    {
        return CurrencyRate::where('base_currency_code', $baseCurrencyCode)
            ->where('target_currency_code', $targetCurrencyCode)
            ->first();
    }

    public function setRate(string $baseCurrencyCode, string $targetCurrencyCode, float $rate): CurrencyRate
    {
        return CurrencyRate::updateOrCreate(
            [
                'base_currency_code' => $baseCurrencyCode,
                'target_currency_code' => $targetCurrencyCode,
            ],
            ['rate' => $rate],
        );
    }
}
