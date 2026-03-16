<?php

declare(strict_types=1);

namespace App\Catalog\Enums;

enum ProductType: string
{
    case Simple = 'simple';
    case Configurable = 'configurable';
    case Bundle = 'bundle';
    case Virtual = 'virtual';
    case Downloadable = 'downloadable';
}
