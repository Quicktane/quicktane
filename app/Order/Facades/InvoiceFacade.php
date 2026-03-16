<?php

declare(strict_types=1);

namespace App\Order\Facades;

use App\Order\Contracts\InvoiceFacade as InvoiceFacadeContract;
use App\Order\Models\Invoice;
use App\Order\Services\InvoiceService;
use Illuminate\Support\Collection;

class InvoiceFacade implements InvoiceFacadeContract
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}

    public function createInvoice(int $orderId, array $data = []): Invoice
    {
        return $this->invoiceService->createInvoice($orderId, $data);
    }

    public function getInvoicesByOrder(int $orderId): Collection
    {
        return $this->invoiceService->getInvoicesByOrder($orderId);
    }
}
