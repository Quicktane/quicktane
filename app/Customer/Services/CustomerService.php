<?php

declare(strict_types=1);

namespace App\Customer\Services;

use App\Customer\Events\AfterCustomerDelete;
use App\Customer\Events\AfterCustomerRegister;
use App\Customer\Events\AfterCustomerUpdate;
use App\Customer\Events\BeforeCustomerDelete;
use App\Customer\Events\BeforeCustomerRegister;
use App\Customer\Events\BeforeCustomerUpdate;
use App\Customer\Models\Customer;
use App\Customer\Repositories\CustomerGroupRepository;
use App\Customer\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Quicktane\Core\Events\EventDispatcher;
use Quicktane\Core\Events\OperationContext;
use Quicktane\Core\Trace\OperationTracer;

class CustomerService
{
    public function __construct(
        private readonly CustomerRepository $customerRepository,
        private readonly CustomerGroupRepository $customerGroupRepository,
        private readonly OperationTracer $operationTracer,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    public function register(array $data): Customer
    {
        return $this->operationTracer->execute('customer.register', function () use ($data): Customer {
            $data = $this->resolveCustomerGroupId($data);

            if (isset($data['password']) && ! str_starts_with($data['password'], '$2y$')) {
                $data['password'] = Hash::make($data['password']);
            }

            $context = new OperationContext;
            $context->set('data', $data);

            $this->eventDispatcher->dispatch(new BeforeCustomerRegister($context));

            if (! isset($data['customer_group_id'])) {
                $defaultGroup = $this->customerGroupRepository->findDefault();
                if ($defaultGroup !== null) {
                    $data['customer_group_id'] = $defaultGroup->id;
                }
            }

            $customer = $this->customerRepository->create($data);

            $this->eventDispatcher->dispatch(new AfterCustomerRegister($customer, $context));

            return $customer;
        });
    }

    public function updateProfile(Customer $customer, array $data): Customer
    {
        return $this->operationTracer->execute('customer.update', function () use ($customer, $data): Customer {
            $data = $this->resolveCustomerGroupId($data);

            $context = new OperationContext;
            $context->set('data', $data);
            $context->set('customer_id', $customer->id);

            $this->eventDispatcher->dispatch(new BeforeCustomerUpdate($customer, $context));

            $customer = $this->customerRepository->update($customer, $data);

            $this->eventDispatcher->dispatch(new AfterCustomerUpdate($customer, $context));

            return $customer;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function resolveCustomerGroupId(array $data): array
    {
        if (isset($data['customer_group_id']) && ! is_int($data['customer_group_id'])) {
            $group = $this->customerGroupRepository->findByUuid($data['customer_group_id']);
            $data['customer_group_id'] = $group?->id;
        }

        return $data;
    }

    public function deleteAccount(Customer $customer): bool
    {
        return $this->operationTracer->execute('customer.delete', function () use ($customer): bool {
            $context = new OperationContext;
            $context->set('customer_id', $customer->id);
            $context->set('customer_email', $customer->email);

            $this->eventDispatcher->dispatch(new BeforeCustomerDelete($customer, $context));

            $result = $this->customerRepository->delete($customer);

            $this->eventDispatcher->dispatch(new AfterCustomerDelete($customer, $context));

            return $result;
        });
    }

    public function changePassword(Customer $customer, string $currentPassword, string $newPassword): void
    {
        if (! Hash::check($currentPassword, $customer->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $this->customerRepository->update($customer, [
            'password' => Hash::make($newPassword),
        ]);
    }
}
