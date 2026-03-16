<?php

declare(strict_types=1);

namespace Quicktane\Search\Contracts;

interface SearchFacade
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function searchProducts(string $query, array $filters, string $sort, int $page, int $perPage): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function autocomplete(string $query, int $limit): array;
}
