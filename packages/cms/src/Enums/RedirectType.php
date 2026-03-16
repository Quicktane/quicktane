<?php

declare(strict_types=1);

namespace Quicktane\CMS\Enums;

enum RedirectType: int
{
    case Permanent = 301;
    case Temporary = 302;
}
