<?php

declare(strict_types=1);

namespace App\Order\Services;

use App\Order\Models\Invoice;
use App\Order\Models\InvoiceItem;
use App\Order\Repositories\InvoiceRepository;
use App\Order\Repositories\OrderRepository;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Quicktane\Core\Trace\OperationTracer;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepository $invoiceRepository,
        private readonly OrderRepository $orderRepository,
        private readonly IncrementIdGenerator $incrementIdGenerator,
        private readonly OperationTracer $operationTracer,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createInvoice(int $orderId, array $data = []): Invoice
    {
        return $this->operationTracer->execute('invoice.create', function () use ($orderId): Invoice {
            $order = $this->orderRepository->findById($orderId);

            if ($order === null) {
                throw ValidationException::withMessages([
                    'order' => ['Order not found.'],
                ]);
            }

            $order->load('items');

            $invoice = $this->invoiceRepository->create([
                'order_id' => $orderId,
                'increment_id' => $this->incrementIdGenerator->nextInvoiceId(),
                'subtotal' => $order->subtotal,
                'shipping_amount' => $order->shipping_amount,
                'discount_amount' => $order->discount_amount,
                'tax_amount' => $order->tax_amount,
                'grand_total' => $order->grand_total,
            ]);

            foreach ($order->items as $orderItem) {
                InvoiceItem::query()->create([
                    'invoice_id' => $invoice->id,
                    'order_item_id' => $orderItem->id,
                    'quantity' => $orderItem->quantity,
                    'row_total' => $orderItem->row_total,
                    'tax_amount' => $orderItem->tax_amount,
                ]);
            }

            $this->orderRepository->update($order, [
                'total_paid' => $order->grand_total,
            ]);

            $invoice->load('items');

            return $invoice;
        });
    }

    public function getInvoicesByOrder(int $orderId): Collection
    {
        return $this->invoiceRepository->findByOrder($orderId);
    }
}
