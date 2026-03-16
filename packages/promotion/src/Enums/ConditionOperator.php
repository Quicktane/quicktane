<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Enums;

enum ConditionOperator: string
{
    case Equals = '==';
    case NotEquals = '!=';
    case GreaterThan = '>';
    case GreaterOrEqual = '>=';
    case LessThan = '<';
    case LessOrEqual = '<=';
    case In = 'in';
    case NotIn = 'not_in';
    case Contains = 'contains';
}
