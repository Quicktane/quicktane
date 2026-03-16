<?php

declare(strict_types=1);

namespace Quicktane\Tax\Enums;

enum TaxCalculationType: string
{
    case Exclusive = 'exclusive';
    case Inclusive = 'inclusive';
}
