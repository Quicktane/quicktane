<?php

declare(strict_types=1);

namespace Quicktane\Search\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Quicktane\Search\Services\IndexService;

class SearchAdminController extends Controller
{
    public function __construct(
        private readonly IndexService $indexService,
    ) {}

    public function reindex(): JsonResponse
    {
        $this->indexService->reindexProducts();

        return response()->json(['message' => 'Product reindexing started.']);
    }

    public function status(): JsonResponse
    {
        return response()->json([
            'engine' => config('scout.driver'),
            'queue_indexing' => config('search.search.queue_indexing'),
        ]);
    }
}
