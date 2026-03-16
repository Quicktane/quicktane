<?php

declare(strict_types=1);

namespace App\Payment\Repositories;

use App\Payment\Models\PaymentMethod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MysqlPaymentMethodRepository implements PaymentMethodRepository
{
    public function __construct(
        private readonly PaymentMethod $paymentMethodModel,
    ) {}

    public function findById(int $id): ?PaymentMethod
    {
        return $this->paymentMethodModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?PaymentMethod
    {
        return $this->paymentMethodModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCode(string $code): ?PaymentMethod
    {
        return $this->paymentMethodModel->newQuery()->where('code', $code)->first();
    }

    public function findActive(): Collection
    {
        return $this->paymentMethodModel->newQuery()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->paymentMethodModel->newQuery();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('sort_order')->paginate($perPage);
    }

    public function create(array $data): PaymentMethod
    {
        return $this->paymentMethodModel->newQuery()->create($data);
    }

    public function update(PaymentMethod $paymentMethod, array $data): PaymentMethod
    {
        $paymentMethod->update($data);

        return $paymentMethod;
    }

    public function delete(PaymentMethod $paymentMethod): void
    {
        $paymentMethod->delete();
    }
}
