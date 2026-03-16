<?php

declare(strict_types=1);

namespace Quicktane\Search\Facades;

use Quicktane\Search\Contracts\SearchFacade as SearchFacadeContract;
use Quicktane\Search\Services\ProductSearchService;

class SearchFacade implements SearchFacadeContract
{
    public function __construct(
        private readonly ProductSearchService $productSearchService,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function searchProducts(string $query, array $filters, string $sort, int $page, int $perPage): array
    {
        return $this->productSearchService->search($query, $filters, $sort, $page, $perPage);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function autocomplete(string $query, int $limit): array
    {
        return $this->productSearchService->autocomplete($query, $limit);
    }
}
