<?php

declare(strict_types=1);

namespace Quicktane\CMS\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\CMS\Http\Requests\StoreUrlRewriteRequest;
use Quicktane\CMS\Http\Requests\UpdateUrlRewriteRequest;
use Quicktane\CMS\Http\Resources\UrlRewriteResource;
use Quicktane\CMS\Models\UrlRewrite;
use Quicktane\CMS\Repositories\UrlRewriteRepository;

class UrlRewriteController extends Controller
{
    public function __construct(
        private readonly UrlRewriteRepository $urlRewriteRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['search', 'entity_type', 'store_view_id']);

        return UrlRewriteResource::collection(
            $this->urlRewriteRepository->paginate($filters, $perPage),
        );
    }

    public function store(StoreUrlRewriteRequest $request): UrlRewriteResource
    {
        $urlRewrite = $this->urlRewriteRepository->create($request->validated());

        return new UrlRewriteResource($urlRewrite);
    }

    public function show(UrlRewrite $urlRewrite): UrlRewriteResource
    {
        return new UrlRewriteResource($urlRewrite);
    }

    public function update(UpdateUrlRewriteRequest $request, UrlRewrite $urlRewrite): UrlRewriteResource
    {
        $urlRewrite = $this->urlRewriteRepository->update($urlRewrite, $request->validated());

        return new UrlRewriteResource($urlRewrite);
    }

    public function destroy(UrlRewrite $urlRewrite): JsonResponse
    {
        $this->urlRewriteRepository->delete($urlRewrite);

        return response()->json(null, 204);
    }
}
