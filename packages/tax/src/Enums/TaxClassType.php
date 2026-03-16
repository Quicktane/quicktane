<?php

declare(strict_types=1);

namespace Quicktane\Tax\Enums;

enum TaxClassType: string
{
    case Product = 'product';
    case Customer = 'customer';
}
