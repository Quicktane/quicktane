<?php

declare(strict_types=1);

namespace App\Cart\Repositories;

use App\Cart\Models\Cart;
use App\Cart\Models\CartStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MysqlCartRepository implements CartRepository
{
    public function __construct(
        private readonly Cart $cartModel,
    ) {}

    public function findById(int $id): ?Cart
    {
        return $this->cartModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Cart
    {
        return $this->cartModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findActiveByCustomer(int $customerId): ?Cart
    {
        return $this->cartModel->newQuery()
            ->where('customer_id', $customerId)
            ->where('status', CartStatus::Active)
            ->first();
    }

    public function findActiveByGuestToken(string $guestToken): ?Cart
    {
        return $this->cartModel->newQuery()
            ->where('guest_token', $guestToken)
            ->where('status', CartStatus::Active)
            ->first();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->cartModel->newQuery()->with('customer');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('customer', function ($query) use ($search): void {
                $query->where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Cart
    {
        return $this->cartModel->newQuery()->create($data);
    }

    public function update(Cart $cart, array $data): Cart
    {
        $cart->update($data);

        return $cart;
    }
}
