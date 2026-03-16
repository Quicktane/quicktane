<?php

declare(strict_types=1);

namespace App\Cart\Events;

use App\Cart\Models\CartItem;
use Quicktane\Core\Events\OperationContext;

class AfterCartItemAdd
{
    public function __construct(
        public readonly CartItem $cartItem,
        public readonly OperationContext $context,
    ) {}
}
