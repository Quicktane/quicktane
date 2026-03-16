<?php

declare(strict_types=1);

namespace Quicktane\Tax\Database\Seeders;

use App\Directory\Models\Country;
use Illuminate\Database\Seeder;
use Quicktane\Tax\Models\TaxClass;
use Quicktane\Tax\Models\TaxRate;
use Quicktane\Tax\Models\TaxRule;
use Quicktane\Tax\Models\TaxZone;
use Quicktane\Tax\Models\TaxZoneRule;

class SampleTaxSeeder extends Seeder
{
    public function run(): void
    {
        $unitedStates = Country::where('iso2', 'US')->first();
        $unitedKingdom = Country::where('iso2', 'GB')->first();
        $germany = Country::where('iso2', 'DE')->first();
        $canada = Country::where('iso2', 'CA')->first();

        $productTaxClass = TaxClass::where('name', 'Taxable Goods')->first();
        $customerTaxClass = TaxClass::where('name', 'Retail Customer')->first();

        if ($productTaxClass === null || $customerTaxClass === null) {
            return;
        }

        // US Tax Zone
        $usZone = TaxZone::firstOrCreate(
            ['name' => 'United States'],
            [
                'description' => 'Tax zone for United States',
                'is_active' => true,
                'sort_order' => 0,
            ],
        );

        if ($unitedStates !== null) {
            TaxZoneRule::firstOrCreate([
                'tax_zone_id' => $usZone->id,
                'country_id' => $unitedStates->id,
            ]);
        }

        $usRate = TaxRate::firstOrCreate(
            ['name' => 'US Sales Tax'],
            [
                'tax_zone_id' => $usZone->id,
                'rate' => 8.875,
                'priority' => 0,
                'is_compound' => false,
                'is_active' => true,
            ],
        );

        TaxRule::firstOrCreate(
            ['name' => 'US Standard Tax'],
            [
                'tax_rate_id' => $usRate->id,
                'product_tax_class_id' => $productTaxClass->id,
                'customer_tax_class_id' => $customerTaxClass->id,
                'priority' => 0,
                'is_active' => true,
            ],
        );

        // UK Tax Zone
        $ukZone = TaxZone::firstOrCreate(
            ['name' => 'United Kingdom'],
            [
                'description' => 'Tax zone for United Kingdom',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        if ($unitedKingdom !== null) {
            TaxZoneRule::firstOrCreate([
                'tax_zone_id' => $ukZone->id,
                'country_id' => $unitedKingdom->id,
            ]);
        }

        $ukRate = TaxRate::firstOrCreate(
            ['name' => 'UK VAT Standard'],
            [
                'tax_zone_id' => $ukZone->id,
                'rate' => 20.0,
                'priority' => 0,
                'is_compound' => false,
                'is_active' => true,
            ],
        );

        TaxRule::firstOrCreate(
            ['name' => 'UK Standard VAT'],
            [
                'tax_rate_id' => $ukRate->id,
                'product_tax_class_id' => $productTaxClass->id,
                'customer_tax_class_id' => $customerTaxClass->id,
                'priority' => 0,
                'is_active' => true,
            ],
        );

        // EU/Germany Tax Zone
        $euZone = TaxZone::firstOrCreate(
            ['name' => 'European Union'],
            [
                'description' => 'Tax zone for EU countries',
                'is_active' => true,
                'sort_order' => 2,
            ],
        );

        if ($germany !== null) {
            TaxZoneRule::firstOrCreate([
                'tax_zone_id' => $euZone->id,
                'country_id' => $germany->id,
            ]);
        }

        $euRate = TaxRate::firstOrCreate(
            ['name' => 'EU VAT Standard'],
            [
                'tax_zone_id' => $euZone->id,
                'rate' => 19.0,
                'priority' => 0,
                'is_compound' => false,
                'is_active' => true,
            ],
        );

        TaxRule::firstOrCreate(
            ['name' => 'EU Standard VAT'],
            [
                'tax_rate_id' => $euRate->id,
                'product_tax_class_id' => $productTaxClass->id,
                'customer_tax_class_id' => $customerTaxClass->id,
                'priority' => 0,
                'is_active' => true,
            ],
        );

        // Canada Tax Zone
        $caZone = TaxZone::firstOrCreate(
            ['name' => 'Canada'],
            [
                'description' => 'Tax zone for Canada',
                'is_active' => true,
                'sort_order' => 3,
            ],
        );

        if ($canada !== null) {
            TaxZoneRule::firstOrCreate([
                'tax_zone_id' => $caZone->id,
                'country_id' => $canada->id,
            ]);
        }

        $caRate = TaxRate::firstOrCreate(
            ['name' => 'Canada GST'],
            [
                'tax_zone_id' => $caZone->id,
                'rate' => 5.0,
                'priority' => 0,
                'is_compound' => false,
                'is_active' => true,
            ],
        );

        TaxRule::firstOrCreate(
            ['name' => 'Canada GST Rule'],
            [
                'tax_rate_id' => $caRate->id,
                'product_tax_class_id' => $productTaxClass->id,
                'customer_tax_class_id' => $customerTaxClass->id,
                'priority' => 0,
                'is_active' => true,
            ],
        );
    }
}
