<?php

declare(strict_types=1);

use App\Cart\CartServiceProvider;
use App\Catalog\CatalogServiceProvider;
use App\Checkout\CheckoutServiceProvider;
use App\Customer\CustomerServiceProvider;
use App\Directory\DirectoryServiceProvider;
use App\Order\OrderServiceProvider;
use App\Payment\PaymentServiceProvider;
use App\Store\StoreServiceProvider;
use App\User\UserServiceProvider;
use Quicktane\CMS\CMSServiceProvider;
use Quicktane\Inventory\InventoryServiceProvider;
use Quicktane\Media\MediaServiceProvider;
use Quicktane\Notification\NotificationServiceProvider;
use Quicktane\Promotion\PromotionServiceProvider;
use Quicktane\Search\SearchServiceProvider;
use Quicktane\Shipping\ShippingServiceProvider;
use Quicktane\Tax\TaxServiceProvider;

return [
    /*
    |--------------------------------------------------------------------------
    | Active Modules
    |--------------------------------------------------------------------------
    |
    | List of module service providers to register. Order matters — modules
    | listed first are registered first.
    |
    */
    'modules' => [
        StoreServiceProvider::class,
        DirectoryServiceProvider::class,
        UserServiceProvider::class,
        MediaServiceProvider::class,
        CatalogServiceProvider::class,
        InventoryServiceProvider::class,
        CustomerServiceProvider::class,
        CartServiceProvider::class,
        TaxServiceProvider::class,
        ShippingServiceProvider::class,
        PaymentServiceProvider::class,
        PromotionServiceProvider::class,
        OrderServiceProvider::class,
        CheckoutServiceProvider::class,
        CMSServiceProvider::class,
        SearchServiceProvider::class,
        NotificationServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Local Modules Path
    |--------------------------------------------------------------------------
    |
    | Directory for project-specific modules. Each subdirectory is auto-discovered
    | as a module if it contains a {Name}ServiceProvider.php file.
    | Local modules are registered AFTER all platform modules above.
    | Set to null to disable auto-discovery.
    |
    */
    'local_path' => app_path(),

    /*
    |--------------------------------------------------------------------------
    | Interface Replacements
    |--------------------------------------------------------------------------
    |
    | Override default facade bindings. Key = interface FQCN, value = new
    | concrete FQCN. Applied after all modules boot.
    |
    */
    'replacements' => [],

    /*
    |--------------------------------------------------------------------------
    | Pipeline Step Replacements
    |--------------------------------------------------------------------------
    |
    | Override default pipeline steps. Key = pipeline name, value = array
    | of [original step FQCN => replacement step FQCN].
    |
    */
    'pipeline_replacements' => [],
];
