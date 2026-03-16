<?php

declare(strict_types=1);

namespace App\Checkout\Repositories;

use App\Checkout\Models\CheckoutSession;

class MysqlCheckoutSessionRepository implements CheckoutSessionRepository
{
    public function __construct(
        private readonly CheckoutSession $checkoutSessionModel,
    ) {}

    public function findById(int $id): ?CheckoutSession
    {
        return $this->checkoutSessionModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?CheckoutSession
    {
        return $this->checkoutSessionModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCart(int $cartId): ?CheckoutSession
    {
        return $this->checkoutSessionModel->newQuery()->where('cart_id', $cartId)->first();
    }

    public function create(array $data): CheckoutSession
    {
        return $this->checkoutSessionModel->newQuery()->create($data);
    }

    public function update(CheckoutSession $checkoutSession, array $data): CheckoutSession
    {
        $checkoutSession->update($data);

        return $checkoutSession;
    }

    public function delete(CheckoutSession $checkoutSession): void
    {
        $checkoutSession->delete();
    }
}
