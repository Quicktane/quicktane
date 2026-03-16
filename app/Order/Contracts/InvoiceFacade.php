<?php

declare(strict_types=1);

namespace App\Order\Contracts;

use App\Order\Models\Invoice;
use Illuminate\Support\Collection;

interface InvoiceFacade
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createInvoice(int $orderId, array $data = []): Invoice;

    public function getInvoicesByOrder(int $orderId): Collection;
}
