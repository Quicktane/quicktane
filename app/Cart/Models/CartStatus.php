<?php

declare(strict_types=1);

namespace App\Cart\Models;

enum CartStatus: string
{
    case Active = 'active';
    case Converted = 'converted';
    case Abandoned = 'abandoned';
    case Merged = 'merged';
}
