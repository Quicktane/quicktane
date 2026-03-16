<?php

declare(strict_types=1);

namespace Quicktane\Search\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Search\Models\SearchSynonym;

class MysqlSearchSynonymRepository implements SearchSynonymRepository
{
    public function __construct(
        private readonly SearchSynonym $searchSynonymModel,
    ) {}

    public function findById(int $id): ?SearchSynonym
    {
        return $this->searchSynonymModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?SearchSynonym
    {
        return $this->searchSynonymModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->searchSynonymModel->newQuery();

        if (isset($filters['search'])) {
            $query->where('term', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['store_view_id'])) {
            $query->where('store_view_id', $filters['store_view_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findByTerm(string $term): ?SearchSynonym
    {
        return $this->searchSynonymModel->newQuery()->where('term', $term)->first();
    }

    public function create(array $data): SearchSynonym
    {
        return $this->searchSynonymModel->newQuery()->create($data);
    }

    public function update(SearchSynonym $searchSynonym, array $data): SearchSynonym
    {
        $searchSynonym->update($data);

        return $searchSynonym;
    }

    public function delete(SearchSynonym $searchSynonym): void
    {
        $searchSynonym->delete();
    }
}
