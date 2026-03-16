<?php

declare(strict_types=1);

namespace App\Payment\Repositories;

use App\Payment\Models\TransactionLog;
use Illuminate\Support\Collection;

interface TransactionLogRepository
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): TransactionLog;

    /**
     * @return Collection<int, TransactionLog>
     */
    public function findByTransaction(int $transactionId): Collection;
}
