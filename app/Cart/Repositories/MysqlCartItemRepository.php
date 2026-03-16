<?php

declare(strict_types=1);

namespace App\Cart\Repositories;

use App\Cart\Models\CartItem;
use Illuminate\Support\Collection;

class MysqlCartItemRepository implements CartItemRepository
{
    public function __construct(
        private readonly CartItem $cartItemModel,
    ) {}

    public function findById(int $id): ?CartItem
    {
        return $this->cartItemModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?CartItem
    {
        return $this->cartItemModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCartAndProduct(int $cartId, int $productId, ?array $options = null): ?CartItem
    {
        $query = $this->cartItemModel->newQuery()
            ->where('cart_id', $cartId)
            ->where('product_id', $productId);

        if ($options !== null) {
            $query->where('options', json_encode($options));
        } else {
            $query->whereNull('options');
        }

        return $query->first();
    }

    public function getByCart(int $cartId): Collection
    {
        return $this->cartItemModel->newQuery()
            ->where('cart_id', $cartId)
            ->get();
    }

    public function create(array $data): CartItem
    {
        return $this->cartItemModel->newQuery()->create($data);
    }

    public function update(CartItem $item, array $data): CartItem
    {
        $item->update($data);

        return $item;
    }

    public function delete(CartItem $item): bool
    {
        return (bool) $item->delete();
    }

    public function deleteByCart(int $cartId): int
    {
        return $this->cartItemModel->newQuery()
            ->where('cart_id', $cartId)
            ->delete();
    }
}
