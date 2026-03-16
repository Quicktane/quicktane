<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Enums;

enum ConditionType: string
{
    case Combine = 'combine';
    case CartAttribute = 'cart_attribute';
    case ProductAttribute = 'product_attribute';
    case CustomerAttribute = 'customer_attribute';
}
