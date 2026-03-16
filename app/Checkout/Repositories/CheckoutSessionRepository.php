<?php

declare(strict_types=1);

namespace App\Checkout\Repositories;

use App\Checkout\Models\CheckoutSession;

interface CheckoutSessionRepository
{
    public function findById(int $id): ?CheckoutSession;

    public function findByUuid(string $uuid): ?CheckoutSession;

    public function findByCart(int $cartId): ?CheckoutSession;

    public function create(array $data): CheckoutSession;

    public function update(CheckoutSession $checkoutSession, array $data): CheckoutSession;

    public function delete(CheckoutSession $checkoutSession): void;
}
