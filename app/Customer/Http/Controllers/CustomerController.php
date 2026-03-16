<?php

declare(strict_types=1);

namespace App\Customer\Http\Controllers;

use App\Customer\Http\Requests\StoreCustomerRequest;
use App\Customer\Http\Requests\UpdateCustomerRequest;
use App\Customer\Http\Resources\CustomerDetailResource;
use App\Customer\Http\Resources\CustomerResource;
use App\Customer\Models\Customer;
use App\Customer\Repositories\CustomerRepository;
use App\Customer\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
        private readonly CustomerRepository $customerRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['search', 'customer_group_id', 'is_active', 'store_id']);

        $customers = $this->customerRepository->paginate($filters, $perPage);

        return CustomerResource::collection($customers);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->register($request->validated());
        $customer->load('group');

        return (new CustomerDetailResource($customer))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Customer $customer): CustomerDetailResource
    {
        $customer->load('group', 'addresses');

        return new CustomerDetailResource($customer);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): CustomerDetailResource
    {
        $customer = $this->customerService->updateProfile($customer, $request->validated());
        $customer->load('group', 'addresses');

        return new CustomerDetailResource($customer);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->deleteAccount($customer);

        return response()->json(null, 204);
    }
}
