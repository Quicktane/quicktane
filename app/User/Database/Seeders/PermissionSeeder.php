<?php

declare(strict_types=1);

namespace App\User\Database\Seeders;

use App\User\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['module' => 'user', 'group_name' => 'Users', 'name' => 'View Users', 'slug' => 'user.users.view'],
            ['module' => 'user', 'group_name' => 'Users', 'name' => 'Manage Users', 'slug' => 'user.users.manage'],
            ['module' => 'user', 'group_name' => 'Roles', 'name' => 'View Roles', 'slug' => 'user.roles.view'],
            ['module' => 'user', 'group_name' => 'Roles', 'name' => 'Manage Roles', 'slug' => 'user.roles.manage'],
            ['module' => 'store', 'group_name' => 'Websites', 'name' => 'View Websites', 'slug' => 'store.websites.view'],
            ['module' => 'store', 'group_name' => 'Websites', 'name' => 'Manage Websites', 'slug' => 'store.websites.manage'],
            ['module' => 'store', 'group_name' => 'Stores', 'name' => 'View Stores', 'slug' => 'store.stores.view'],
            ['module' => 'store', 'group_name' => 'Stores', 'name' => 'Manage Stores', 'slug' => 'store.stores.manage'],
            ['module' => 'store', 'group_name' => 'Store Views', 'name' => 'View Store Views', 'slug' => 'store.store-views.view'],
            ['module' => 'store', 'group_name' => 'Store Views', 'name' => 'Manage Store Views', 'slug' => 'store.store-views.manage'],
            ['module' => 'store', 'group_name' => 'Configuration', 'name' => 'View Configuration', 'slug' => 'store.config.view'],
            ['module' => 'store', 'group_name' => 'Configuration', 'name' => 'Manage Configuration', 'slug' => 'store.config.manage'],
            ['module' => 'directory', 'group_name' => 'Countries', 'name' => 'View Countries', 'slug' => 'directory.countries.view'],
            ['module' => 'directory', 'group_name' => 'Countries', 'name' => 'Manage Countries', 'slug' => 'directory.countries.manage'],
            ['module' => 'directory', 'group_name' => 'Currencies', 'name' => 'View Currencies', 'slug' => 'directory.currencies.view'],
            ['module' => 'directory', 'group_name' => 'Currencies', 'name' => 'Manage Currencies', 'slug' => 'directory.currencies.manage'],
            ['module' => 'directory', 'group_name' => 'Currency Rates', 'name' => 'View Currency Rates', 'slug' => 'directory.currency-rates.view'],
            ['module' => 'directory', 'group_name' => 'Currency Rates', 'name' => 'Manage Currency Rates', 'slug' => 'directory.currency-rates.manage'],

            ['module' => 'media', 'group_name' => 'Media Files', 'name' => 'View Media Files', 'slug' => 'media.files.view'],
            ['module' => 'media', 'group_name' => 'Media Files', 'name' => 'Manage Media Files', 'slug' => 'media.files.manage'],

            ['module' => 'catalog', 'group_name' => 'Attributes', 'name' => 'View Attributes', 'slug' => 'catalog.attributes.view'],
            ['module' => 'catalog', 'group_name' => 'Attributes', 'name' => 'Manage Attributes', 'slug' => 'catalog.attributes.manage'],
            ['module' => 'catalog', 'group_name' => 'Categories', 'name' => 'View Categories', 'slug' => 'catalog.categories.view'],
            ['module' => 'catalog', 'group_name' => 'Categories', 'name' => 'Manage Categories', 'slug' => 'catalog.categories.manage'],
            ['module' => 'catalog', 'group_name' => 'Products', 'name' => 'View Products', 'slug' => 'catalog.products.view'],
            ['module' => 'catalog', 'group_name' => 'Products', 'name' => 'Manage Products', 'slug' => 'catalog.products.manage'],

            ['module' => 'inventory', 'group_name' => 'Inventory Sources', 'name' => 'View Inventory Sources', 'slug' => 'inventory.sources.view'],
            ['module' => 'inventory', 'group_name' => 'Inventory Sources', 'name' => 'Manage Inventory Sources', 'slug' => 'inventory.sources.manage'],
            ['module' => 'inventory', 'group_name' => 'Stock', 'name' => 'View Stock', 'slug' => 'inventory.stock.view'],
            ['module' => 'inventory', 'group_name' => 'Stock', 'name' => 'Manage Stock', 'slug' => 'inventory.stock.manage'],

            ['module' => 'customer', 'group_name' => 'Customers', 'name' => 'View Customers', 'slug' => 'customer.customers.view'],
            ['module' => 'customer', 'group_name' => 'Customers', 'name' => 'Manage Customers', 'slug' => 'customer.customers.manage'],
            ['module' => 'customer', 'group_name' => 'Customer Groups', 'name' => 'View Customer Groups', 'slug' => 'customer.groups.view'],
            ['module' => 'customer', 'group_name' => 'Customer Groups', 'name' => 'Manage Customer Groups', 'slug' => 'customer.groups.manage'],

            ['module' => 'cart', 'group_name' => 'Carts', 'name' => 'View Carts', 'slug' => 'cart.carts.view'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission,
            );
        }
    }
}
