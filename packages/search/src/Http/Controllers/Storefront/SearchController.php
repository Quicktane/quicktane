<?php

declare(strict_types=1);

namespace Quicktane\Search\Http\Controllers\Storefront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Quicktane\Search\Contracts\SearchFacade;
use Quicktane\Search\Http\Resources\SearchResultResource;

class SearchController extends Controller
{
    public function __construct(
        private readonly SearchFacade $searchFacade,
    ) {}

    public function search(Request $request): SearchResultResource
    {
        $request->validate([
            'q' => ['required', 'string', 'min:1'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string'],
        ]);

        $results = $this->searchFacade->searchProducts(
            query: $request->query('q'),
            filters: $request->only(['category_id', 'is_active', 'price_min', 'price_max']),
            sort: (string) $request->query('sort', ''),
            page: (int) $request->query('page', 1),
            perPage: (int) $request->query('per_page', config('search.search.products_per_page', 24)),
        );

        return new SearchResultResource($results);
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['required', 'string', 'min:1'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);

        $suggestions = $this->searchFacade->autocomplete(
            query: $request->query('q'),
            limit: (int) $request->query('limit', config('search.search.autocomplete_limit', 10)),
        );

        return response()->json(['data' => $suggestions]);
    }
}
