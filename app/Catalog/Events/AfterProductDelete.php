<?php

declare(strict_types=1);

namespace App\Catalog\Events;

use App\Catalog\Models\Product;
use Quicktane\Core\Events\OperationContext;

class AfterProductDelete
{
    public function __construct(
        public readonly Product $product,
        public readonly OperationContext $context,
    ) {}
}
