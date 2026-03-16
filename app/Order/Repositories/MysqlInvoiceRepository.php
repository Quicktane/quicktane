<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\Invoice;
use Illuminate\Support\Collection;

class MysqlInvoiceRepository implements InvoiceRepository
{
    public function __construct(
        private readonly Invoice $invoiceModel,
    ) {}

    public function findById(int $id): ?Invoice
    {
        return $this->invoiceModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Invoice
    {
        return $this->invoiceModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByOrder(int $orderId): Collection
    {
        return $this->invoiceModel->newQuery()
            ->where('order_id', $orderId)
            ->with('items')
            ->get();
    }

    public function create(array $data): Invoice
    {
        return $this->invoiceModel->newQuery()->create($data);
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);

        return $invoice;
    }
}
