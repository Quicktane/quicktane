<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Enums;

enum ConditionAggregator: string
{
    case All = 'all';
    case Any = 'any';
}
