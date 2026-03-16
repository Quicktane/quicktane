<?php

declare(strict_types=1);

namespace App\Cart\Events;

use App\Cart\Models\CartItem;
use Quicktane\Core\Events\OperationContext;

class BeforeCartItemRemove
{
    public function __construct(
        public readonly CartItem $cartItem,
        public readonly OperationContext $context,
    ) {}
}
