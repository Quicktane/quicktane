<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Enums;

enum ActionType: string
{
    case ByPercent = 'by_percent';
    case ByFixed = 'by_fixed';
    case BuyXGetY = 'buy_x_get_y';
    case FreeShipping = 'free_shipping';
}
