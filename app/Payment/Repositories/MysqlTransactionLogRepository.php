<?php

declare(strict_types=1);

namespace App\Payment\Repositories;

use App\Payment\Models\TransactionLog;
use Illuminate\Support\Collection;

class MysqlTransactionLogRepository implements TransactionLogRepository
{
    public function __construct(
        private readonly TransactionLog $transactionLogModel,
    ) {}

    public function create(array $data): TransactionLog
    {
        return $this->transactionLogModel->newQuery()->create(array_merge($data, [
            'created_at' => now(),
        ]));
    }

    public function findByTransaction(int $transactionId): Collection
    {
        return $this->transactionLogModel->newQuery()
            ->where('transaction_id', $transactionId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
