<?php

declare(strict_types=1);

namespace App\Payment\Repositories;

use App\Payment\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MysqlTransactionRepository implements TransactionRepository
{
    public function __construct(
        private readonly Transaction $transactionModel,
    ) {}

    public function findById(int $id): ?Transaction
    {
        return $this->transactionModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Transaction
    {
        return $this->transactionModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByReference(string $referenceId): ?Transaction
    {
        return $this->transactionModel->newQuery()->where('reference_id', $referenceId)->first();
    }

    public function findByOrder(int $orderId): Collection
    {
        return $this->transactionModel->newQuery()
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->transactionModel->newQuery();

        if (isset($filters['order_id'])) {
            $query->where('order_id', $filters['order_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['payment_method_code'])) {
            $query->where('payment_method_code', $filters['payment_method_code']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Transaction
    {
        return $this->transactionModel->newQuery()->create($data);
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->update($data);

        return $transaction;
    }
}
