<?php

declare(strict_types=1);

namespace App\Customer\Database\Seeders;

use App\Customer\Models\Customer;
use App\Customer\Models\CustomerAddress;
use App\Customer\Models\CustomerGroup;
use App\Directory\Models\Country;
use App\Store\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleCustomerSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('code', 'main_store')->first();
        $generalGroup = CustomerGroup::where('code', 'general')->first();
        $wholesaleGroup = CustomerGroup::where('code', 'wholesale')->first();
        $vipGroup = CustomerGroup::where('code', 'vip')->first();

        if ($store === null || $generalGroup === null) {
            return;
        }

        $unitedStates = Country::where('iso2', 'US')->first();
        $unitedKingdom = Country::where('iso2', 'GB')->first();
        $germany = Country::where('iso2', 'DE')->first();
        $canada = Country::where('iso2', 'CA')->first();

        $customers = [
            [
                'email' => 'john.doe@example.com',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '+1-555-0101',
                'gender' => 'male',
                'date_of_birth' => '1990-05-15',
                'group' => $generalGroup,
                'addresses' => [
                    [
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'street_line_1' => '123 Main Street',
                        'street_line_2' => 'Apt 4B',
                        'city' => 'New York',
                        'postcode' => '10001',
                        'country' => $unitedStates,
                        'phone' => '+1-555-0101',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                ],
            ],
            [
                'email' => 'jane.smith@example.com',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'phone' => '+1-555-0102',
                'gender' => 'female',
                'date_of_birth' => '1985-11-22',
                'group' => $generalGroup,
                'addresses' => [
                    [
                        'first_name' => 'Jane',
                        'last_name' => 'Smith',
                        'street_line_1' => '456 Oak Avenue',
                        'city' => 'Los Angeles',
                        'postcode' => '90001',
                        'country' => $unitedStates,
                        'phone' => '+1-555-0102',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                    [
                        'first_name' => 'Jane',
                        'last_name' => 'Smith',
                        'company' => 'Smith Corp',
                        'street_line_1' => '789 Business Park',
                        'street_line_2' => 'Suite 200',
                        'city' => 'Los Angeles',
                        'postcode' => '90002',
                        'country' => $unitedStates,
                        'phone' => '+1-555-0103',
                        'is_default_billing' => false,
                        'is_default_shipping' => false,
                    ],
                ],
            ],
            [
                'email' => 'robert.wilson@example.com',
                'first_name' => 'Robert',
                'last_name' => 'Wilson',
                'phone' => '+44-20-7946-0101',
                'gender' => 'male',
                'date_of_birth' => '1978-03-08',
                'group' => $wholesaleGroup,
                'addresses' => [
                    [
                        'first_name' => 'Robert',
                        'last_name' => 'Wilson',
                        'company' => 'Wilson Trading Ltd',
                        'street_line_1' => '10 Downing Street',
                        'city' => 'London',
                        'postcode' => 'SW1A 2AA',
                        'country' => $unitedKingdom,
                        'phone' => '+44-20-7946-0101',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                ],
            ],
            [
                'email' => 'maria.garcia@example.com',
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'phone' => '+1-555-0104',
                'gender' => 'female',
                'date_of_birth' => '1992-07-30',
                'group' => $generalGroup,
                'addresses' => [
                    [
                        'first_name' => 'Maria',
                        'last_name' => 'Garcia',
                        'street_line_1' => '321 Elm Drive',
                        'city' => 'Chicago',
                        'postcode' => '60601',
                        'country' => $unitedStates,
                        'phone' => '+1-555-0104',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                ],
            ],
            [
                'email' => 'hans.mueller@example.com',
                'first_name' => 'Hans',
                'last_name' => 'Mueller',
                'phone' => '+49-30-1234567',
                'gender' => 'male',
                'date_of_birth' => '1988-12-01',
                'group' => $vipGroup ?? $generalGroup,
                'addresses' => [
                    [
                        'first_name' => 'Hans',
                        'last_name' => 'Mueller',
                        'street_line_1' => 'Friedrichstrasse 43',
                        'city' => 'Berlin',
                        'postcode' => '10117',
                        'country' => $germany,
                        'phone' => '+49-30-1234567',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                ],
            ],
            [
                'email' => 'emily.chen@example.com',
                'first_name' => 'Emily',
                'last_name' => 'Chen',
                'phone' => '+1-555-0105',
                'gender' => 'female',
                'date_of_birth' => '1995-09-17',
                'group' => $generalGroup,
                'addresses' => [
                    [
                        'first_name' => 'Emily',
                        'last_name' => 'Chen',
                        'street_line_1' => '555 Pine Street',
                        'city' => 'San Francisco',
                        'postcode' => '94102',
                        'country' => $unitedStates,
                        'phone' => '+1-555-0105',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                ],
            ],
            [
                'email' => 'david.brown@example.com',
                'first_name' => 'David',
                'last_name' => 'Brown',
                'phone' => '+1-416-555-0101',
                'gender' => 'male',
                'date_of_birth' => '1982-01-25',
                'group' => $wholesaleGroup ?? $generalGroup,
                'addresses' => [
                    [
                        'first_name' => 'David',
                        'last_name' => 'Brown',
                        'company' => 'Brown Imports Inc',
                        'street_line_1' => '200 Bay Street',
                        'street_line_2' => 'Floor 15',
                        'city' => 'Toronto',
                        'postcode' => 'M5J 2J5',
                        'country' => $canada,
                        'phone' => '+1-416-555-0101',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                ],
            ],
            [
                'email' => 'sarah.johnson@example.com',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'phone' => '+1-555-0106',
                'gender' => 'female',
                'date_of_birth' => '1991-04-12',
                'group' => $vipGroup ?? $generalGroup,
                'addresses' => [
                    [
                        'first_name' => 'Sarah',
                        'last_name' => 'Johnson',
                        'street_line_1' => '888 Fifth Avenue',
                        'street_line_2' => 'Penthouse A',
                        'city' => 'New York',
                        'postcode' => '10022',
                        'country' => $unitedStates,
                        'phone' => '+1-555-0106',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                ],
            ],
            [
                'email' => 'michael.lee@example.com',
                'first_name' => 'Michael',
                'last_name' => 'Lee',
                'phone' => '+1-555-0107',
                'gender' => 'male',
                'date_of_birth' => '1993-08-05',
                'group' => $generalGroup,
                'addresses' => [
                    [
                        'first_name' => 'Michael',
                        'last_name' => 'Lee',
                        'street_line_1' => '42 Sunset Blvd',
                        'city' => 'Houston',
                        'postcode' => '77001',
                        'country' => $unitedStates,
                        'phone' => '+1-555-0107',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                ],
            ],
            [
                'email' => 'amanda.taylor@example.com',
                'first_name' => 'Amanda',
                'last_name' => 'Taylor',
                'phone' => '+44-121-555-0101',
                'gender' => 'female',
                'date_of_birth' => '1987-06-20',
                'group' => $generalGroup,
                'addresses' => [
                    [
                        'first_name' => 'Amanda',
                        'last_name' => 'Taylor',
                        'street_line_1' => '25 Corporation Street',
                        'city' => 'Birmingham',
                        'postcode' => 'B2 4QJ',
                        'country' => $unitedKingdom,
                        'phone' => '+44-121-555-0101',
                        'is_default_billing' => true,
                        'is_default_shipping' => true,
                    ],
                ],
            ],
        ];

        foreach ($customers as $customerData) {
            $customer = Customer::firstOrCreate(
                ['email' => $customerData['email']],
                [
                    'store_id' => $store->id,
                    'customer_group_id' => $customerData['group']->id,
                    'password' => Hash::make('password123'),
                    'first_name' => $customerData['first_name'],
                    'last_name' => $customerData['last_name'],
                    'phone' => $customerData['phone'],
                    'gender' => $customerData['gender'],
                    'date_of_birth' => $customerData['date_of_birth'],
                    'is_active' => true,
                ],
            );

            if ($customer->wasRecentlyCreated) {
                foreach ($customerData['addresses'] as $addressData) {
                    CustomerAddress::create([
                        'customer_id' => $customer->id,
                        'first_name' => $addressData['first_name'],
                        'last_name' => $addressData['last_name'],
                        'company' => $addressData['company'] ?? null,
                        'street_line_1' => $addressData['street_line_1'],
                        'street_line_2' => $addressData['street_line_2'] ?? null,
                        'city' => $addressData['city'],
                        'postcode' => $addressData['postcode'],
                        'country_id' => $addressData['country']?->iso2,
                        'phone' => $addressData['phone'],
                        'is_default_billing' => $addressData['is_default_billing'],
                        'is_default_shipping' => $addressData['is_default_shipping'],
                    ]);
                }
            }
        }
    }
}
