<?php

declare(strict_types=1);

namespace App\Cart\Events;

use App\Cart\Models\Cart;

class AfterCartMerge
{
    public function __construct(
        public readonly Cart $customerCart,
        public readonly Cart $guestCart,
    ) {}
}
