<?php

declare(strict_types=1);

namespace App\Customer\Http\Controllers\Storefront;

use App\Customer\Http\Requests\LoginCustomerRequest;
use App\Customer\Http\Requests\RegisterCustomerRequest;
use App\Customer\Http\Resources\CustomerResource;
use App\Customer\Models\Customer;
use App\Customer\Services\CustomerAuthService;
use App\Customer\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private readonly CustomerAuthService $customerAuthService,
        private readonly CustomerService $customerService,
    ) {}

    public function register(RegisterCustomerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $customer = $this->customerService->register($data);
        $customer->load('group');

        $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'customer' => new CustomerResource($customer),
        ], 201);
    }

    public function login(LoginCustomerRequest $request): JsonResponse
    {
        $result = $this->customerAuthService->login(
            $request->validated('email'),
            $request->validated('password'),
            (int) $request->validated('store_id'),
        );

        return response()->json([
            'token' => $result['token'],
            'customer' => new CustomerResource($result['customer']),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var Customer $customer */
        $customer = $request->user();
        $this->customerAuthService->logout($customer);

        return response()->json([
            'message' => 'Successfully logged out.',
        ]);
    }
}
