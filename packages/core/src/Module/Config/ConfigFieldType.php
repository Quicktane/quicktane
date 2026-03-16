<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Config;

enum ConfigFieldType: string
{
    case String = 'string';
    case Boolean = 'boolean';
    case Integer = 'integer';
    case Select = 'select';
    case Encrypted = 'encrypted';
}
