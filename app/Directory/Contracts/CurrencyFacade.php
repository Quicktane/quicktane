<?php

declare(strict_types=1);

namespace App\Directory\Contracts;

use App\Directory\Models\CurrencyRate;
use Illuminate\Support\Collection;

interface CurrencyFacade
{
    public function convert(float $amount, string $fromCode, string $toCode): float;

    public function format(float $amount, string $currencyCode): string;

    public function listCurrencies(bool $activeOnly = false): Collection;

    public function getRate(string $baseCurrencyCode, string $targetCurrencyCode): ?CurrencyRate;

    public function availableForStoreView(int $storeViewId): Collection;
}
