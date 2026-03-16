<?php

declare(strict_types=1);

namespace App\Directory\Repositories;

use App\Directory\Models\Currency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MysqlCurrencyRepository implements CurrencyRepository
{
    public function all(bool $activeOnly = false): Collection
    {
        $query = Currency::query()->orderBy('sort_order')->orderBy('name');

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->get();
    }

    public function findByCode(string $code): ?Currency
    {
        return Currency::where('code', $code)->first();
    }

    public function findById(int $id): ?Currency
    {
        return Currency::find($id);
    }

    public function update(Currency $currency, array $data): Currency
    {
        $currency->update($data);

        return $currency->refresh();
    }

    public function getForStoreView(int $storeViewId): Collection
    {
        $currencyIds = DB::table('store_view_currencies')
            ->where('store_view_id', $storeViewId)
            ->pluck('currency_id');

        if ($currencyIds->isEmpty()) {
            return Currency::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        }

        return Currency::whereIn('id', $currencyIds)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
