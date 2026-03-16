<?php

declare(strict_types=1);

namespace App\Customer\Listeners;

use App\Cart\Contracts\CartFacade;
use App\Customer\Events\AfterCustomerLogin;
use Quicktane\Core\Module\ModuleRegistry;

class MergeGuestCartListener
{
    public function __construct(
        private readonly ModuleRegistry $moduleRegistry,
    ) {}

    public function handle(AfterCustomerLogin $event): void
    {
        if (! $this->moduleRegistry->has('cart')) {
            return;
        }

        $guestToken = request()->header('X-Cart-Token');

        if ($guestToken === null) {
            return;
        }

        /** @var CartFacade $cartFacade */
        $cartFacade = app(CartFacade::class);
        $cartFacade->mergeGuestCart($guestToken, $event->customer->id);
    }
}
