<?php

declare(strict_types=1);

namespace App\Customer\Services;

use App\Customer\Events\AfterCustomerLogin;
use App\Customer\Models\Customer;
use App\Customer\Repositories\CustomerRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Quicktane\Core\Events\EventDispatcher;

class CustomerAuthService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @return array{token: string, customer: Customer}
     *
     * @throws AuthenticationException
     */
    public function login(string $email, string $password, int $storeId): array
    {
        $customer = $this->customerRepository->findByEmail($email, $storeId);

        if ($customer === null || ! Hash::check($password, $customer->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        if (! $customer->is_active) {
            throw new AuthenticationException('Account is deactivated.');
        }

        $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;

        $customer->last_login_at = now();
        $customer->save();

        $customer->load('group');

        $this->eventDispatcher->dispatch(new AfterCustomerLogin($customer));

        return [
            'token' => $token,
            'customer' => $customer,
        ];
    }

    public function logout(Customer $customer): void
    {
        $customer->currentAccessToken()->delete();
    }

    public function logoutAll(Customer $customer): void
    {
        $customer->tokens()->delete();
    }
}
