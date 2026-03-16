<?php

declare(strict_types=1);

namespace Quicktane\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Quicktane\Core\Module\Menu\MenuItem;
use Quicktane\Core\Module\Menu\MenuRegistry;

class MenuController
{
    public function index(MenuRegistry $menuRegistry): JsonResponse
    {
        $tree = $menuRegistry->tree();

        return response()->json([
            'data' => array_map(fn (MenuItem $menuItem): array => $menuItem->toArray(), $tree),
        ]);
    }
}
