<?php

declare(strict_types=1);

namespace App\Store\Http\Controllers;

use App\Store\Http\Requests\StoreWebsiteRequest;
use App\Store\Http\Requests\UpdateWebsiteRequest;
use App\Store\Http\Resources\WebsiteResource;
use App\Store\Models\Website;
use App\Store\Repositories\WebsiteRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class WebsiteController extends Controller
{
    public function __construct(
        private readonly WebsiteRepository $websiteRepository,
    ) {}

    public function index(): JsonResponse
    {
        $websites = $this->websiteRepository->all();

        return response()->json([
            'data' => WebsiteResource::collection($websites),
        ]);
    }

    public function store(StoreWebsiteRequest $request): JsonResponse
    {
        $website = $this->websiteRepository->create($request->validated());

        return response()->json([
            'data' => new WebsiteResource($website->load('stores')),
        ], 201);
    }

    public function show(Website $website): JsonResponse
    {
        return response()->json([
            'data' => new WebsiteResource($website->load('stores')),
        ]);
    }

    public function update(UpdateWebsiteRequest $request, Website $website): JsonResponse
    {
        $website = $this->websiteRepository->update($website, $request->validated());

        return response()->json([
            'data' => new WebsiteResource($website),
        ]);
    }

    public function destroy(Website $website): JsonResponse
    {
        $this->websiteRepository->delete($website);

        return response()->json(null, 204);
    }
}
