<?php

declare(strict_types=1);

namespace Quicktane\Search\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\Search\Http\Requests\StoreSearchSynonymRequest;
use Quicktane\Search\Http\Requests\UpdateSearchSynonymRequest;
use Quicktane\Search\Http\Resources\SearchSynonymResource;
use Quicktane\Search\Models\SearchSynonym;
use Quicktane\Search\Repositories\SearchSynonymRepository;

class SynonymController extends Controller
{
    public function __construct(
        private readonly SearchSynonymRepository $searchSynonymRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['search', 'is_active', 'store_view_id']);

        return SearchSynonymResource::collection(
            $this->searchSynonymRepository->paginate($filters, $perPage),
        );
    }

    public function store(StoreSearchSynonymRequest $request): SearchSynonymResource
    {
        $searchSynonym = $this->searchSynonymRepository->create($request->validated());

        return new SearchSynonymResource($searchSynonym);
    }

    public function show(SearchSynonym $searchSynonym): SearchSynonymResource
    {
        return new SearchSynonymResource($searchSynonym);
    }

    public function update(UpdateSearchSynonymRequest $request, SearchSynonym $searchSynonym): SearchSynonymResource
    {
        $searchSynonym = $this->searchSynonymRepository->update($searchSynonym, $request->validated());

        return new SearchSynonymResource($searchSynonym);
    }

    public function destroy(SearchSynonym $searchSynonym): JsonResponse
    {
        $this->searchSynonymRepository->delete($searchSynonym);

        return response()->json(null, 204);
    }
}
