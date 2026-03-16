<?php

declare(strict_types=1);

namespace Quicktane\Tax\Database\Seeders;

use Illuminate\Database\Seeder;
use Quicktane\Tax\Enums\TaxClassType;
use Quicktane\Tax\Models\TaxClass;

class DefaultTaxClassSeeder extends Seeder
{
    public function run(): void
    {
        $taxClasses = [
            ['name' => 'Taxable Goods', 'type' => TaxClassType::Product, 'is_default' => true],
            ['name' => 'None', 'type' => TaxClassType::Product, 'is_default' => false],
            ['name' => 'Retail Customer', 'type' => TaxClassType::Customer, 'is_default' => true],
            ['name' => 'Tax Exempt', 'type' => TaxClassType::Customer, 'is_default' => false],
        ];

        foreach ($taxClasses as $taxClassData) {
            TaxClass::query()->firstOrCreate(
                ['name' => $taxClassData['name'], 'type' => $taxClassData['type']],
                $taxClassData,
            );
        }
    }
}
