<?php

declare(strict_types=1);

namespace App\Directory\Repositories;

use App\Directory\Models\CurrencyRate;
use Illuminate\Support\Collection;

interface CurrencyRateRepository
{
    public function all(): Collection;

    public function getRate(string $baseCurrencyCode, string $targetCurrencyCode): ?CurrencyRate;

    public function setRate(string $baseCurrencyCode, string $targetCurrencyCode, float $rate): CurrencyRate;
}
