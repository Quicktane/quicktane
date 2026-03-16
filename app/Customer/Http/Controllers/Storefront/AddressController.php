<?php

declare(strict_types=1);

namespace App\Customer\Http\Controllers\Storefront;

use App\Customer\Http\Requests\StoreCustomerAddressRequest;
use App\Customer\Http\Requests\UpdateCustomerAddressRequest;
use App\Customer\Http\Resources\CustomerAddressResource;
use App\Customer\Models\Customer;
use App\Customer\Models\CustomerAddress;
use App\Customer\Repositories\CustomerAddressRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class AddressController extends Controller
{
    public function __construct(
        private readonly CustomerAddressRepository $customerAddressRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var Customer $customer */
        $customer = $request->user();
        $addresses = $this->customerAddressRepository->getByCustomer($customer->id);

        return CustomerAddressResource::collection($addresses);
    }

    public function store(StoreCustomerAddressRequest $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->user();
        $data = $request->validated();
        $data['customer_id'] = $customer->id;

        if (! empty($data['is_default_billing'])) {
            $this->customerAddressRepository->clearDefaultBilling($customer->id);
        }

        if (! empty($data['is_default_shipping'])) {
            $this->customerAddressRepository->clearDefaultShipping($customer->id);
        }

        $address = $this->customerAddressRepository->create($data);

        return (new CustomerAddressResource($address))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCustomerAddressRequest $request, CustomerAddress $address): CustomerAddressResource
    {
        /** @var Customer $customer */
        $customer = $request->user();

        if ($address->customer_id !== $customer->id) {
            abort(404);
        }

        $data = $request->validated();

        if (! empty($data['is_default_billing'])) {
            $this->customerAddressRepository->clearDefaultBilling($customer->id);
        }

        if (! empty($data['is_default_shipping'])) {
            $this->customerAddressRepository->clearDefaultShipping($customer->id);
        }

        $address = $this->customerAddressRepository->update($address, $data);

        return new CustomerAddressResource($address);
    }

    public function destroy(Request $request, CustomerAddress $address): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->user();

        if ($address->customer_id !== $customer->id) {
            abort(404);
        }

        $this->customerAddressRepository->delete($address);

        return response()->json(null, 204);
    }
}
