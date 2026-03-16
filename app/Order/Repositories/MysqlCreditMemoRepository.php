<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\CreditMemo;
use Illuminate\Support\Collection;

class MysqlCreditMemoRepository implements CreditMemoRepository
{
    public function __construct(
        private readonly CreditMemo $creditMemoModel,
    ) {}

    public function findById(int $id): ?CreditMemo
    {
        return $this->creditMemoModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?CreditMemo
    {
        return $this->creditMemoModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByOrder(int $orderId): Collection
    {
        return $this->creditMemoModel->newQuery()
            ->where('order_id', $orderId)
            ->with('items')
            ->get();
    }

    public function create(array $data): CreditMemo
    {
        return $this->creditMemoModel->newQuery()->create($data);
    }

    public function update(CreditMemo $creditMemo, array $data): CreditMemo
    {
        $creditMemo->update($data);

        return $creditMemo;
    }
}
