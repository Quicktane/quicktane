<?php

declare(strict_types=1);

namespace App\Customer\Http\Controllers\Storefront;

use App\Customer\Http\Requests\ChangePasswordRequest;
use App\Customer\Http\Requests\UpdateProfileRequest;
use App\Customer\Http\Resources\CustomerDetailResource;
use App\Customer\Models\Customer;
use App\Customer\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProfileController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    public function show(Request $request): CustomerDetailResource
    {
        /** @var Customer $customer */
        $customer = $request->user();
        $customer->load('group', 'addresses');

        return new CustomerDetailResource($customer);
    }

    public function update(UpdateProfileRequest $request): CustomerDetailResource
    {
        /** @var Customer $customer */
        $customer = $request->user();
        $customer = $this->customerService->updateProfile($customer, $request->validated());
        $customer->load('group', 'addresses');

        return new CustomerDetailResource($customer);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->user();

        $this->customerService->changePassword(
            $customer,
            $request->validated('current_password'),
            $request->validated('password'),
        );

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }
}
