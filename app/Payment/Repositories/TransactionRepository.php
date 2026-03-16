<?php

declare(strict_types=1);

namespace App\Payment\Repositories;

use App\Payment\Models\Transaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TransactionRepository
{
    public function findById(int $id): ?Transaction;

    public function findByUuid(string $uuid): ?Transaction;

    public function findByReference(string $referenceId): ?Transaction;

    /**
     * @return Collection<int, Transaction>
     */
    public function findByOrder(int $orderId): Collection;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Transaction;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Transaction $transaction, array $data): Transaction;
}
