<?php

declare(strict_types=1);

namespace App\Catalog\Enums;

enum AttributeType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case Select = 'select';
    case Multiselect = 'multiselect';
    case Boolean = 'boolean';
    case Decimal = 'decimal';
    case Integer = 'integer';
    case Date = 'date';
}
