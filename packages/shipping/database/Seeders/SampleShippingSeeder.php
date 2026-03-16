<?php

declare(strict_types=1);

namespace Quicktane\Shipping\Database\Seeders;

use App\Directory\Models\Country;
use Illuminate\Database\Seeder;
use Quicktane\Shipping\Models\ShippingMethod;
use Quicktane\Shipping\Models\ShippingRate;
use Quicktane\Shipping\Models\ShippingZone;
use Quicktane\Shipping\Models\ShippingZoneCountry;

class SampleShippingSeeder extends Seeder
{
    public function run(): void
    {
        $unitedStates = Country::where('iso2', 'US')->first();
        $unitedKingdom = Country::where('iso2', 'GB')->first();
        $germany = Country::where('iso2', 'DE')->first();
        $canada = Country::where('iso2', 'CA')->first();

        $euCountryCodes = ['FR', 'IT', 'ES', 'NL', 'BE', 'AT', 'PL', 'SE', 'PT', 'CZ', 'IE', 'DK', 'FI', 'NO', 'CH'];
        $euCountries = Country::whereIn('iso2', $euCountryCodes)->get();

        // Shipping Methods
        $standardShipping = ShippingMethod::firstOrCreate(
            ['code' => 'standard'],
            [
                'name' => 'Standard Shipping',
                'carrier_code' => 'flat_rate',
                'description' => 'Standard delivery in 5-7 business days',
                'is_active' => true,
                'sort_order' => 0,
            ],
        );

        $expressShipping = ShippingMethod::firstOrCreate(
            ['code' => 'express'],
            [
                'name' => 'Express Shipping',
                'carrier_code' => 'flat_rate',
                'description' => 'Express delivery in 2-3 business days',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        $overnightShipping = ShippingMethod::firstOrCreate(
            ['code' => 'overnight'],
            [
                'name' => 'Overnight Shipping',
                'carrier_code' => 'flat_rate',
                'description' => 'Next business day delivery',
                'is_active' => true,
                'sort_order' => 2,
            ],
        );

        $freeShipping = ShippingMethod::firstOrCreate(
            ['code' => 'free'],
            [
                'name' => 'Free Shipping',
                'carrier_code' => 'flat_rate',
                'description' => 'Free shipping on qualifying orders',
                'is_active' => true,
                'sort_order' => 3,
                'min_order_amount' => 100.00,
                'free_shipping_threshold' => 100.00,
            ],
        );

        // Shipping Zones
        $domesticZone = ShippingZone::firstOrCreate(
            ['name' => 'Domestic (US)'],
            ['is_active' => true],
        );

        $europeZone = ShippingZone::firstOrCreate(
            ['name' => 'Europe'],
            ['is_active' => true],
        );

        $internationalZone = ShippingZone::firstOrCreate(
            ['name' => 'International'],
            ['is_active' => true],
        );

        // Zone Countries
        $zoneCountries = [
            $domesticZone->id => [$unitedStates, $canada],
            $europeZone->id => array_merge(
                [$unitedKingdom, $germany],
                $euCountries->all(),
            ),
        ];

        foreach ($zoneCountries as $zoneId => $countries) {
            foreach ($countries as $country) {
                if ($country !== null) {
                    ShippingZoneCountry::firstOrCreate([
                        'shipping_zone_id' => $zoneId,
                        'country_id' => $country->id,
                    ]);
                }
            }
        }

        // All remaining active countries go to International zone
        $assignedCountryIds = ShippingZoneCountry::pluck('country_id')->toArray();
        $internationalCountries = Country::where('is_active', true)
            ->whereNotIn('id', $assignedCountryIds)
            ->get();

        foreach ($internationalCountries as $country) {
            ShippingZoneCountry::firstOrCreate([
                'shipping_zone_id' => $internationalZone->id,
                'country_id' => $country->id,
            ]);
        }

        // Shipping Rates
        $rates = [
            // Standard domestic
            ['method' => $standardShipping, 'zone' => $domesticZone, 'price' => 5.99, 'min_subtotal' => 0, 'max_subtotal' => 99.99],
            ['method' => $standardShipping, 'zone' => $europeZone, 'price' => 12.99, 'min_subtotal' => 0, 'max_subtotal' => null],
            ['method' => $standardShipping, 'zone' => $internationalZone, 'price' => 19.99, 'min_subtotal' => 0, 'max_subtotal' => null],
            // Express domestic
            ['method' => $expressShipping, 'zone' => $domesticZone, 'price' => 14.99, 'min_subtotal' => 0, 'max_subtotal' => null],
            ['method' => $expressShipping, 'zone' => $europeZone, 'price' => 24.99, 'min_subtotal' => 0, 'max_subtotal' => null],
            // Overnight domestic only
            ['method' => $overnightShipping, 'zone' => $domesticZone, 'price' => 29.99, 'min_subtotal' => 0, 'max_subtotal' => null],
            // Free domestic
            ['method' => $freeShipping, 'zone' => $domesticZone, 'price' => 0.00, 'min_subtotal' => 100.00, 'max_subtotal' => null],
        ];

        foreach ($rates as $rateData) {
            ShippingRate::firstOrCreate(
                [
                    'shipping_method_id' => $rateData['method']->id,
                    'shipping_zone_id' => $rateData['zone']->id,
                    'min_subtotal' => $rateData['min_subtotal'],
                ],
                [
                    'price' => $rateData['price'],
                    'max_subtotal' => $rateData['max_subtotal'],
                    'is_active' => true,
                ],
            );
        }
    }
}
