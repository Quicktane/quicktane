<?php

declare(strict_types=1);

namespace Quicktane\CMS\Enums;

enum PageLayout: string
{
    case OneColumn = 'one_column';
    case TwoColumnsLeft = 'two_columns_left';
    case TwoColumnsRight = 'two_columns_right';
    case Empty = 'empty';
}
