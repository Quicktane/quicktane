<?php

declare(strict_types=1);

namespace Quicktane\CMS\Enums;

enum EntityType: string
{
    case Product = 'product';
    case Category = 'category';
    case CmsPage = 'cms_page';
    case Custom = 'custom';
}
