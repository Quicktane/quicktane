<?php

declare(strict_types=1);

namespace Quicktane\Search\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Search\Models\SearchSynonym;

interface SearchSynonymRepository
{
    public function findById(int $id): ?SearchSynonym;

    public function findByUuid(string $uuid): ?SearchSynonym;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findByTerm(string $term): ?SearchSynonym;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): SearchSynonym;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(SearchSynonym $searchSynonym, array $data): SearchSynonym;

    public function delete(SearchSynonym $searchSynonym): void;
}
