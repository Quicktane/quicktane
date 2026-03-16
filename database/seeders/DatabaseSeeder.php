<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Catalog\Database\Seeders\DefaultAttributeSetSeeder;
use App\Catalog\Database\Seeders\DefaultCategorySeeder;
use App\Catalog\Database\Seeders\SampleAttributeSeeder;
use App\Catalog\Database\Seeders\SampleCategorySeeder;
use App\Catalog\Database\Seeders\SampleProductSeeder;
use App\Customer\Database\Seeders\CustomerGroupSeeder;
use App\Customer\Database\Seeders\SampleCustomerSeeder;
use App\Directory\Database\Seeders\CountrySeeder;
use App\Directory\Database\Seeders\CurrencySeeder;
use App\Order\Database\Seeders\SampleOrderSeeder;
use App\Payment\Database\Seeders\SamplePaymentMethodSeeder;
use App\Store\Database\Seeders\DefaultStoreSeeder;
use App\User\Database\Seeders\PermissionSeeder;
use App\User\Database\Seeders\RoleSeeder;
use App\User\Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;
use Quicktane\Inventory\Database\Seeders\DefaultInventorySourceSeeder;
use Quicktane\Promotion\Database\Seeders\SamplePromotionSeeder;
use Quicktane\Shipping\Database\Seeders\SampleShippingSeeder;
use Quicktane\Tax\Database\Seeders\DefaultTaxClassSeeder;
use Quicktane\Tax\Database\Seeders\SampleTaxSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Foundation data
        $this->call([
            DefaultStoreSeeder::class,
            CountrySeeder::class,
            CurrencySeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            DefaultAttributeSetSeeder::class,
            SampleAttributeSeeder::class,
            DefaultCategorySeeder::class,
            DefaultInventorySourceSeeder::class,
            CustomerGroupSeeder::class,
            DefaultTaxClassSeeder::class,
        ]);

        // Sample data
        $this->call([
            SampleCategorySeeder::class,
            SampleProductSeeder::class,
            SampleCustomerSeeder::class,
            SampleTaxSeeder::class,
            SampleShippingSeeder::class,
            SamplePaymentMethodSeeder::class,
            SamplePromotionSeeder::class,
            SampleOrderSeeder::class,
        ]);
    }
}
