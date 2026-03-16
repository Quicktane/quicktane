<?php

declare(strict_types=1);

namespace App\Cart\Repositories;

use App\Cart\Models\CartItem;
use Illuminate\Support\Collection;

interface CartItemRepository
{
    public function findById(int $id): ?CartItem;

    public function findByUuid(string $uuid): ?CartItem;

    public function findByCartAndProduct(int $cartId, int $productId, ?array $options = null): ?CartItem;

    public function getByCart(int $cartId): Collection;

    public function create(array $data): CartItem;

    public function update(CartItem $item, array $data): CartItem;

    public function delete(CartItem $item): bool;

    public function deleteByCart(int $cartId): int;
}
