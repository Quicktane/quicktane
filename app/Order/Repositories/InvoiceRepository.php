<?php

declare(strict_types=1);

namespace App\Order\Repositories;

use App\Order\Models\Invoice;
use Illuminate\Support\Collection;

interface InvoiceRepository
{
    public function findById(int $id): ?Invoice;

    public function findByUuid(string $uuid): ?Invoice;

    public function findByOrder(int $orderId): Collection;

    public function create(array $data): Invoice;

    public function update(Invoice $invoice, array $data): Invoice;
}
