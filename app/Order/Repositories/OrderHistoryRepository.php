<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\OrderHistory;
use Illuminate\Support\Collection;

interface OrderHistoryRepository
{
    public function findByOrder(int $orderId): Collection;

    public function create(array $data): OrderHistory;
}
