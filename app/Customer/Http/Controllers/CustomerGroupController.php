<?php

declare(strict_types=1);

namespace App\Customer\Http\Controllers;

use App\Customer\Http\Requests\StoreCustomerGroupRequest;
use App\Customer\Http\Requests\UpdateCustomerGroupRequest;
use App\Customer\Http\Resources\CustomerGroupResource;
use App\Customer\Models\CustomerGroup;
use App\Customer\Repositories\CustomerGroupRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class CustomerGroupController extends Controller
{
    public function __construct(
        private readonly CustomerGroupRepository $customerGroupRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $groups = $this->customerGroupRepository->paginate($perPage);

        return CustomerGroupResource::collection($groups);
    }

    public function store(StoreCustomerGroupRequest $request): JsonResponse
    {
        $group = $this->customerGroupRepository->create($request->validated());

        return (new CustomerGroupResource($group))
            ->response()
            ->setStatusCode(201);
    }

    public function show(CustomerGroup $group): CustomerGroupResource
    {
        return new CustomerGroupResource($group);
    }

    public function update(UpdateCustomerGroupRequest $request, CustomerGroup $group): CustomerGroupResource
    {
        $group = $this->customerGroupRepository->update($group, $request->validated());

        return new CustomerGroupResource($group);
    }

    public function destroy(CustomerGroup $group): JsonResponse
    {
        if ($group->is_default) {
            return response()->json([
                'message' => 'The default customer group cannot be deleted.',
            ], 403);
        }

        $this->customerGroupRepository->delete($group);

        return response()->json(null, 204);
    }
}
