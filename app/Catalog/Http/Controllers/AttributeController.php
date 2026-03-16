<?php

declare(strict_types=1);

namespace App\Catalog\Http\Controllers;

use App\Catalog\Http\Requests\StoreAttributeRequest;
use App\Catalog\Http\Requests\UpdateAttributeRequest;
use App\Catalog\Http\Resources\AttributeResource;
use App\Catalog\Models\Attribute;
use App\Catalog\Repositories\AttributeOptionRepository;
use App\Catalog\Repositories\AttributeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class AttributeController extends Controller
{
    public function __construct(
        private readonly AttributeRepository $attributeRepository,
        private readonly AttributeOptionRepository $attributeOptionRepository,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $attributes = $this->attributeRepository->all();
        $attributes->load('options');

        return AttributeResource::collection($attributes);
    }

    public function store(StoreAttributeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $options = $data['options'] ?? [];
        unset($data['options']);

        $attribute = $this->attributeRepository->create($data);

        foreach ($options as $option) {
            $this->attributeOptionRepository->create([
                'attribute_id' => $attribute->id,
                'label' => $option['label'],
                'value' => $option['value'],
                'sort_order' => $option['sort_order'] ?? 0,
            ]);
        }

        $attribute->load('options');

        return (new AttributeResource($attribute))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Attribute $attribute): AttributeResource
    {
        $attribute->load('options');

        return new AttributeResource($attribute);
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute): AttributeResource
    {
        $data = $request->validated();
        $options = $data['options'] ?? null;
        unset($data['options']);

        $attribute = $this->attributeRepository->update($attribute, $data);

        if ($options !== null) {
            $this->attributeOptionRepository->deleteByAttribute($attribute->id);

            foreach ($options as $option) {
                $this->attributeOptionRepository->create([
                    'attribute_id' => $attribute->id,
                    'label' => $option['label'],
                    'value' => $option['value'],
                    'sort_order' => $option['sort_order'] ?? 0,
                ]);
            }
        }

        $attribute->load('options');

        return new AttributeResource($attribute);
    }

    public function destroy(Attribute $attribute): JsonResponse
    {
        $this->attributeRepository->delete($attribute);

        return response()->json(null, 204);
    }
}
