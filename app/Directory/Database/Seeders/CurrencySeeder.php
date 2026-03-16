<?php

declare(strict_types=1);

namespace App\Directory\Database\Seeders;

use App\Directory\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = require __DIR__.'/data/currencies.php';

        foreach ($currencies as $currencyData) {
            Currency::firstOrCreate(
                ['code' => $currencyData['code']],
                $currencyData,
            );
        }
    }
}
