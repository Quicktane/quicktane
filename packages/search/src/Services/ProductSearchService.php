<?php

declare(strict_types=1);

namespace Quicktane\Search\Services;

use App\Catalog\Models\Product;

class ProductSearchService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(string $query, array $filters = [], string $sort = '', int $page = 1, int $perPage = 24): array
    {
        $scoutSearch = Product::search($query);

        foreach ($filters as $field => $value) {
            $scoutSearch->where($field, $value);
        }

        if ($sort !== '') {
            $scoutSearch->orderBy($sort);
        }

        $results = $scoutSearch->paginate($perPage, 'page', $page);

        return [
            'products' => $results->items(),
            'total' => $results->total(),
            'facets' => $results->rawResponse()['facetDistribution'] ?? [],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function autocomplete(string $query, int $limit = 10): array
    {
        $results = Product::search($query)
            ->take($limit)
            ->get(['name', 'slug']);

        return $results->map(fn (Product $product): array => [
            'name' => $product->name,
            'slug' => $product->slug,
        ])->toArray();
    }
}
