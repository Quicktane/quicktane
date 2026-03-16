<?php

declare(strict_types=1);

namespace App\Directory\Repositories;

use App\Directory\Models\Currency;
use Illuminate\Support\Collection;

interface CurrencyRepository
{
    public function all(bool $activeOnly = false): Collection;

    public function findByCode(string $code): ?Currency;

    public function findById(int $id): ?Currency;

    public function update(Currency $currency, array $data): Currency;

    public function getForStoreView(int $storeViewId): Collection;
}
