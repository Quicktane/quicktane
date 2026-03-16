<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\OrderHistory;
use Illuminate\Support\Collection;

class MysqlOrderHistoryRepository implements OrderHistoryRepository
{
    public function __construct(
        private readonly OrderHistory $orderHistoryModel,
    ) {}

    public function findByOrder(int $orderId): Collection
    {
        return $this->orderHistoryModel->newQuery()
            ->where('order_id', $orderId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function create(array $data): OrderHistory
    {
        $data['created_at'] = $data['created_at'] ?? now();

        return $this->orderHistoryModel->newQuery()->create($data);
    }
}
