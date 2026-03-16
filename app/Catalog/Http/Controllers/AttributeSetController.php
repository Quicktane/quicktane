<?php

declare(strict_types=1);

namespace App\Catalog\Http\Controllers;

use App\Catalog\Http\Requests\StoreAttributeSetRequest;
use App\Catalog\Http\Requests\UpdateAttributeSetRequest;
use App\Catalog\Http\Resources\AttributeSetResource;
use App\Catalog\Models\AttributeSet;
use App\Catalog\Repositories\AttributeSetRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class AttributeSetController extends Controller
{
    public function __construct(
        private readonly AttributeSetRepository $attributeSetRepository,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $attributeSets = $this->attributeSetRepository->all();

        return AttributeSetResource::collection($attributeSets);
    }

    public function store(StoreAttributeSetRequest $request): JsonResponse
    {
        $attributeSet = $this->attributeSetRepository->create($request->validated());

        return (new AttributeSetResource($attributeSet))
            ->response()
            ->setStatusCode(201);
    }

    public function show(AttributeSet $attributeSet): AttributeSetResource
    {
        $attributeSet->load('attributes');

        return new AttributeSetResource($attributeSet);
    }

    public function update(UpdateAttributeSetRequest $request, AttributeSet $attributeSet): AttributeSetResource
    {
        $attributeSet = $this->attributeSetRepository->update($attributeSet, $request->validated());

        return new AttributeSetResource($attributeSet);
    }

    public function destroy(AttributeSet $attributeSet): JsonResponse
    {
        $this->attributeSetRepository->delete($attributeSet);

        return response()->json(null, 204);
    }

    public function syncAttributes(Request $request, AttributeSet $attributeSet): AttributeSetResource
    {
        $validated = $request->validate([
            'attributes' => ['required', 'array'],
            'attributes.*.attribute_id' => ['required', 'integer', 'exists:attributes,id'],
            'attributes.*.group_name' => ['sometimes', 'string'],
            'attributes.*.sort_order' => ['sometimes', 'integer'],
        ]);

        $syncData = [];

        foreach ($validated['attributes'] as $attributeData) {
            $syncData[$attributeData['attribute_id']] = [
                'group_name' => $attributeData['group_name'] ?? 'General',
                'sort_order' => $attributeData['sort_order'] ?? 0,
            ];
        }

        $attributeSet->attributes()->sync($syncData);
        $attributeSet->load('attributes');

        return new AttributeSetResource($attributeSet);
    }
}
