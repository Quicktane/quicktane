<?php

declare(strict_types=1);

namespace App\Payment\Repositories;

use App\Payment\Models\PaymentMethod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PaymentMethodRepository
{
    public function findById(int $id): ?PaymentMethod;

    public function findByUuid(string $uuid): ?PaymentMethod;

    public function findByCode(string $code): ?PaymentMethod;

    /**
     * @return Collection<int, PaymentMethod>
     */
    public function findActive(): Collection;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): PaymentMethod;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(PaymentMethod $paymentMethod, array $data): PaymentMethod;

    public function delete(PaymentMethod $paymentMethod): void;
}
