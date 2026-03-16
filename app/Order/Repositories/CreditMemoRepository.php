<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\CreditMemo;
use Illuminate\Support\Collection;

interface CreditMemoRepository
{
    public function findById(int $id): ?CreditMemo;

    public function findByUuid(string $uuid): ?CreditMemo;

    public function findByOrder(int $orderId): Collection;

    public function create(array $data): CreditMemo;

    public function update(CreditMemo $creditMemo, array $data): CreditMemo;
}
