<?php

declare(strict_types=1);

namespace App\Order\Services;

use App\Order\Models\CreditMemo;
use App\Order\Models\CreditMemoItem;
use App\Order\Repositories\CreditMemoRepository;
use App\Order\Repositories\OrderRepository;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Quicktane\Core\Trace\OperationTracer;

class CreditMemoService
{
    public function __construct(
        private readonly CreditMemoRepository $creditMemoRepository,
        private readonly OrderRepository $orderRepository,
        private readonly IncrementIdGenerator $incrementIdGenerator,
        private readonly OperationTracer $operationTracer,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createCreditMemo(int $orderId, array $data = []): CreditMemo
    {
        return $this->operationTracer->execute('credit_memo.create', function () use ($orderId, $data): CreditMemo {
            $order = $this->orderRepository->findById($orderId);

            if ($order === null) {
                throw ValidationException::withMessages([
                    'order' => ['Order not found.'],
                ]);
            }

            $order->load('items');

            $adjustmentPositive = $data['adjustment_positive'] ?? '0.0000';
            $adjustmentNegative = $data['adjustment_negative'] ?? '0.0000';

            $grandTotal = bcadd($order->grand_total, $adjustmentPositive, 4);
            $grandTotal = bcsub($grandTotal, $adjustmentNegative, 4);

            $creditMemo = $this->creditMemoRepository->create([
                'order_id' => $orderId,
                'invoice_id' => $data['invoice_id'] ?? null,
                'increment_id' => $this->incrementIdGenerator->nextCreditMemoId(),
                'subtotal' => $order->subtotal,
                'shipping_amount' => $data['refund_shipping'] ?? '0.0000',
                'adjustment_positive' => $adjustmentPositive,
                'adjustment_negative' => $adjustmentNegative,
                'tax_amount' => $order->tax_amount,
                'grand_total' => $grandTotal,
            ]);

            foreach ($order->items as $orderItem) {
                CreditMemoItem::query()->create([
                    'credit_memo_id' => $creditMemo->id,
                    'order_item_id' => $orderItem->id,
                    'quantity' => $orderItem->quantity,
                    'row_total' => $orderItem->row_total,
                    'tax_amount' => $orderItem->tax_amount,
                ]);
            }

            $totalRefunded = bcadd($order->total_refunded, $creditMemo->grand_total, 4);
            $this->orderRepository->update($order, [
                'total_refunded' => $totalRefunded,
            ]);

            $creditMemo->load('items');

            return $creditMemo;
        });
    }

    public function getCreditMemosByOrder(int $orderId): Collection
    {
        return $this->creditMemoRepository->findByOrder($orderId);
    }
}
