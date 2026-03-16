<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\CMS\Http\Requests\StorePageRequest;
use Quicktane\CMS\Http\Requests\UpdatePageRequest;
use Quicktane\CMS\Http\Resources\PageResource;
use Quicktane\CMS\Models\Page;
use Quicktane\CMS\Repositories\PageRepository;
use Quicktane\CMS\Services\PageService;

class PageController extends Controller
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageService $pageService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['search', 'is_active']);

        return PageResource::collection(
            $this->pageRepository->paginate($filters, $perPage),
        );
    }

    public function store(StorePageRequest $request): PageResource
    {
        $validated = $request->validated();
        $storeViewIds = $validated['store_view_ids'] ?? [0];
        unset($validated['store_view_ids']);

        $page = $this->pageService->createPage($validated, $storeViewIds);
        $page->load('storeViews');

        return new PageResource($page);
    }

    public function show(Page $page): PageResource
    {
        $page->load('storeViews');

        return new PageResource($page);
    }

    public function update(UpdatePageRequest $request, Page $page): PageResource
    {
        $validated = $request->validated();
        $storeViewIds = $validated['store_view_ids'] ?? null;
        unset($validated['store_view_ids']);

        $page = $this->pageService->updatePage($page, $validated, $storeViewIds);
        $page->load('storeViews');

        return new PageResource($page);
    }

    public function destroy(Page $page): JsonResponse
    {
        $this->pageService->deletePage($page);

        return response()->json(null, 204);
    }
}
