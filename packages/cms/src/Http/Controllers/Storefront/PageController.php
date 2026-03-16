<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Controllers\Storefront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\CMS\Http\Resources\PageResource;
use Quicktane\CMS\Repositories\PageRepository;
use Quicktane\CMS\Services\UrlRewriteService;

class PageController extends Controller
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly UrlRewriteService $urlRewriteService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $storeViewId = (int) $request->query('store_view_id', 0);

        $pages = $this->pageRepository->findActiveForStoreView($storeViewId);

        return PageResource::collection($pages);
    }

    public function show(string $identifier): PageResource|JsonResponse
    {
        $page = $this->pageRepository->findByIdentifier($identifier);

        if ($page === null || ! $page->is_active) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return new PageResource($page);
    }

    public function resolve(Request $request): JsonResponse
    {
        $path = $request->query('path');
        $storeViewId = (int) $request->query('store_view_id', 0);

        if ($path === null) {
            return response()->json(['message' => 'Path parameter is required'], 400);
        }

        $urlRewrite = $this->urlRewriteService->resolve($path, $storeViewId);

        if ($urlRewrite === null) {
            return response()->json(['message' => 'URL not found'], 404);
        }

        if ($urlRewrite->isRedirect()) {
            return response()->json([
                'redirect' => true,
                'redirect_type' => $urlRewrite->redirect_type->value,
                'target_path' => $urlRewrite->target_path,
            ]);
        }

        return response()->json([
            'redirect' => false,
            'entity_type' => $urlRewrite->entity_type->value,
            'entity_id' => $urlRewrite->entity_id,
            'target_path' => $urlRewrite->target_path,
        ]);
    }
}
