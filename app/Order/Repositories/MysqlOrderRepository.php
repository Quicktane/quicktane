<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MysqlOrderRepository implements OrderRepository
{
    public function __construct(
        private readonly Order $orderModel,
    ) {}

    public function findById(int $id): ?Order
    {
        return $this->orderModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Order
    {
        return $this->orderModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByIncrementId(string $incrementId): ?Order
    {
        return $this->orderModel->newQuery()->where('increment_id', $incrementId)->first();
    }

    public function findByCustomer(int $customerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderModel->newQuery()
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate($perPage);
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->orderModel->newQuery()->with('customer');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search): void {
                $query->where('increment_id', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Order
    {
        return $this->orderModel->newQuery()->create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);

        return $order;
    }
}
