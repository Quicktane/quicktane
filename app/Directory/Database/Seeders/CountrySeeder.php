<?php

declare(strict_types=1);

namespace App\Directory\Database\Seeders;

use App\Directory\Models\Country;
use App\Directory\Models\Region;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = require __DIR__.'/data/countries.php';

        foreach ($countries as $countryData) {
            Country::firstOrCreate(
                ['iso2' => $countryData['iso2']],
                $countryData,
            );
        }

        $regionsPath = __DIR__.'/data/regions';

        foreach (glob($regionsPath.'/*.php') as $regionFile) {
            $iso2 = basename($regionFile, '.php');
            $country = Country::where('iso2', $iso2)->first();

            if ($country === null) {
                continue;
            }

            $regions = require $regionFile;

            foreach ($regions as $regionData) {
                Region::firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'code' => $regionData['code'],
                    ],
                    array_merge($regionData, ['country_id' => $country->id]),
                );
            }
        }
    }
}
