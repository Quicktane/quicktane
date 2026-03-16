<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\OrderItem;
use Illuminate\Support\Collection;

class MysqlOrderItemRepository implements OrderItemRepository
{
    public function __construct(
        private readonly OrderItem $orderItemModel,
    ) {}

    public function findById(int $id): ?OrderItem
    {
        return $this->orderItemModel->newQuery()->find($id);
    }

    public function findByOrder(int $orderId): Collection
    {
        return $this->orderItemModel->newQuery()
            ->where('order_id', $orderId)
            ->get();
    }

    public function create(array $data): OrderItem
    {
        return $this->orderItemModel->newQuery()->create($data);
    }
}
