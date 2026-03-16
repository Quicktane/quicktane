<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\OrderItem;
use Illuminate\Support\Collection;

interface OrderItemRepository
{
    public function findById(int $id): ?OrderItem;

    public function findByOrder(int $orderId): Collection;

    public function create(array $data): OrderItem;
}
