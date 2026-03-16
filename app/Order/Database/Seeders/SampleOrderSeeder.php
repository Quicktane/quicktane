<?php

declare(strict_types=1);

namespace App\Order\Database\Seeders;

use App\Catalog\Models\Product;
use App\Customer\Models\Customer;
use App\Customer\Models\CustomerAddress;
use App\Directory\Models\Country;
use App\Order\Enums\InvoiceStatus;
use App\Order\Enums\OrderAddressType;
use App\Order\Enums\OrderStatus;
use App\Order\Models\Invoice;
use App\Order\Models\InvoiceItem;
use App\Order\Models\Order;
use App\Order\Models\OrderAddress;
use App\Order\Models\OrderHistory;
use App\Order\Models\OrderItem;
use App\Store\Models\Store;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class SampleOrderSeeder extends Seeder
{
    private int $orderCounter = 0;

    private int $invoiceCounter = 0;

    public function run(): void
    {
        $store = Store::where('code', 'main_store')->first();
        $customers = Customer::all();
        $products = Product::where('is_active', true)->get();

        if ($store === null || $customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Skip if orders already exist
        if (Order::count() > 0) {
            return;
        }

        $orders = [
            [
                'customer_email' => 'john.doe@example.com',
                'status' => OrderStatus::Completed,
                'shipping_method_code' => 'standard',
                'shipping_method_label' => 'Standard Shipping',
                'payment_method_code' => 'credit_card',
                'payment_method_label' => 'Credit Card',
                'shipping_amount' => 5.99,
                'items' => [
                    ['sku' => 'MT-001', 'quantity' => 2],
                    ['sku' => 'MJ-001', 'quantity' => 1],
                ],
                'days_ago' => 45,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'jane.smith@example.com',
                'status' => OrderStatus::Processing,
                'shipping_method_code' => 'express',
                'shipping_method_label' => 'Express Shipping',
                'payment_method_code' => 'paypal',
                'payment_method_label' => 'PayPal',
                'shipping_amount' => 14.99,
                'items' => [
                    ['sku' => 'WD-001', 'quantity' => 1],
                    ['sku' => 'WT-001', 'quantity' => 2],
                ],
                'days_ago' => 3,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'robert.wilson@example.com',
                'status' => OrderStatus::Completed,
                'shipping_method_code' => 'standard',
                'shipping_method_label' => 'Standard Shipping',
                'payment_method_code' => 'bank_transfer',
                'payment_method_label' => 'Bank Transfer',
                'shipping_amount' => 12.99,
                'items' => [
                    ['sku' => 'MK-001', 'quantity' => 3],
                    ['sku' => 'MT-002', 'quantity' => 10],
                    ['sku' => 'MJ-002', 'quantity' => 5],
                ],
                'days_ago' => 30,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'maria.garcia@example.com',
                'status' => OrderStatus::Shipped,
                'shipping_method_code' => 'express',
                'shipping_method_label' => 'Express Shipping',
                'payment_method_code' => 'credit_card',
                'payment_method_label' => 'Credit Card',
                'shipping_amount' => 14.99,
                'items' => [
                    ['sku' => 'WD-002', 'quantity' => 1],
                    ['sku' => 'AB-001', 'quantity' => 1],
                ],
                'days_ago' => 5,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'hans.mueller@example.com',
                'status' => OrderStatus::Completed,
                'shipping_method_code' => 'standard',
                'shipping_method_label' => 'Standard Shipping',
                'payment_method_code' => 'credit_card',
                'payment_method_label' => 'Credit Card',
                'shipping_amount' => 12.99,
                'items' => [
                    ['sku' => 'EL-001', 'quantity' => 1],
                    ['sku' => 'EH-001', 'quantity' => 1],
                ],
                'days_ago' => 60,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'emily.chen@example.com',
                'status' => OrderStatus::Pending,
                'shipping_method_code' => 'standard',
                'shipping_method_label' => 'Standard Shipping',
                'payment_method_code' => 'paypal',
                'payment_method_label' => 'PayPal',
                'shipping_amount' => 5.99,
                'items' => [
                    ['sku' => 'ES-001', 'quantity' => 1],
                ],
                'days_ago' => 1,
                'has_invoice' => false,
            ],
            [
                'customer_email' => 'david.brown@example.com',
                'status' => OrderStatus::Completed,
                'shipping_method_code' => 'standard',
                'shipping_method_label' => 'Standard Shipping',
                'payment_method_code' => 'bank_transfer',
                'payment_method_label' => 'Bank Transfer',
                'shipping_amount' => 5.99,
                'items' => [
                    ['sku' => 'EL-002', 'quantity' => 2],
                    ['sku' => 'ES-002', 'quantity' => 5],
                ],
                'days_ago' => 20,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'sarah.johnson@example.com',
                'status' => OrderStatus::Delivered,
                'shipping_method_code' => 'overnight',
                'shipping_method_label' => 'Overnight Shipping',
                'payment_method_code' => 'credit_card',
                'payment_method_label' => 'Credit Card',
                'shipping_amount' => 29.99,
                'items' => [
                    ['sku' => 'WD-002', 'quantity' => 1],
                    ['sku' => 'AW-001', 'quantity' => 1],
                    ['sku' => 'AB-001', 'quantity' => 1],
                ],
                'days_ago' => 10,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'michael.lee@example.com',
                'status' => OrderStatus::Canceled,
                'shipping_method_code' => 'standard',
                'shipping_method_label' => 'Standard Shipping',
                'payment_method_code' => 'credit_card',
                'payment_method_label' => 'Credit Card',
                'shipping_amount' => 5.99,
                'items' => [
                    ['sku' => 'MT-001', 'quantity' => 3],
                ],
                'days_ago' => 15,
                'has_invoice' => false,
                'admin_note' => 'Customer requested cancellation.',
            ],
            [
                'customer_email' => 'amanda.taylor@example.com',
                'status' => OrderStatus::Processing,
                'shipping_method_code' => 'express',
                'shipping_method_label' => 'Express Shipping',
                'payment_method_code' => 'paypal',
                'payment_method_label' => 'PayPal',
                'shipping_amount' => 24.99,
                'items' => [
                    ['sku' => 'WT-002', 'quantity' => 1],
                    ['sku' => 'AW-002', 'quantity' => 1],
                ],
                'days_ago' => 2,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'john.doe@example.com',
                'status' => OrderStatus::Completed,
                'shipping_method_code' => 'standard',
                'shipping_method_label' => 'Standard Shipping',
                'payment_method_code' => 'credit_card',
                'payment_method_label' => 'Credit Card',
                'shipping_amount' => 5.99,
                'coupon_code' => 'WELCOME20',
                'items' => [
                    ['sku' => 'EH-002', 'quantity' => 2],
                    ['sku' => 'AB-002', 'quantity' => 1],
                ],
                'days_ago' => 90,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'jane.smith@example.com',
                'status' => OrderStatus::Refunded,
                'shipping_method_code' => 'standard',
                'shipping_method_label' => 'Standard Shipping',
                'payment_method_code' => 'credit_card',
                'payment_method_label' => 'Credit Card',
                'shipping_amount' => 5.99,
                'items' => [
                    ['sku' => 'MK-002', 'quantity' => 1],
                ],
                'days_ago' => 25,
                'has_invoice' => true,
                'admin_note' => 'Product defective, full refund issued.',
            ],
            [
                'customer_email' => 'sarah.johnson@example.com',
                'status' => OrderStatus::OnHold,
                'shipping_method_code' => 'express',
                'shipping_method_label' => 'Express Shipping',
                'payment_method_code' => 'bank_transfer',
                'payment_method_label' => 'Bank Transfer',
                'shipping_amount' => 14.99,
                'items' => [
                    ['sku' => 'EL-001', 'quantity' => 1],
                ],
                'days_ago' => 4,
                'has_invoice' => false,
                'admin_note' => 'Waiting for bank transfer confirmation.',
            ],
            [
                'customer_email' => 'hans.mueller@example.com',
                'status' => OrderStatus::Completed,
                'shipping_method_code' => 'express',
                'shipping_method_label' => 'Express Shipping',
                'payment_method_code' => 'paypal',
                'payment_method_label' => 'PayPal',
                'shipping_amount' => 24.99,
                'coupon_code' => 'SAVE15',
                'items' => [
                    ['sku' => 'MK-001', 'quantity' => 1],
                    ['sku' => 'MT-001', 'quantity' => 2],
                    ['sku' => 'MJ-001', 'quantity' => 1],
                ],
                'days_ago' => 14,
                'has_invoice' => true,
            ],
            [
                'customer_email' => 'emily.chen@example.com',
                'status' => OrderStatus::Shipped,
                'shipping_method_code' => 'standard',
                'shipping_method_label' => 'Standard Shipping',
                'payment_method_code' => 'credit_card',
                'payment_method_label' => 'Credit Card',
                'shipping_amount' => 0.00,
                'items' => [
                    ['sku' => 'WT-001', 'quantity' => 3],
                    ['sku' => 'WD-001', 'quantity' => 2],
                ],
                'days_ago' => 7,
                'has_invoice' => true,
            ],
        ];

        foreach ($orders as $orderData) {
            $this->createOrder($orderData, $store, $products);
        }

        $this->syncRedisSequences();
    }

    private function syncRedisSequences(): void
    {
        $maxOrderId = Order::max('increment_id');
        $maxInvoiceId = Invoice::max('increment_id');

        if ($maxOrderId !== null) {
            Redis::connection()->set('sequence:order_increment_id', (int) $maxOrderId);
        }

        if ($maxInvoiceId !== null) {
            Redis::connection()->set('sequence:invoice_increment_id', (int) $maxInvoiceId);
        }
    }

    /**
     * @param  array<string, mixed>  $orderData
     * @param  Collection<int, Product>  $products
     */
    private function createOrder(array $orderData, Store $store, $products): void
    {
        $customer = Customer::where('email', $orderData['customer_email'])->first();

        if ($customer === null) {
            return;
        }

        $this->orderCounter++;
        $incrementId = '1000'.str_pad((string) $this->orderCounter, 5, '0', STR_PAD_LEFT);
        $createdAt = now()->subDays($orderData['days_ago']);

        // Calculate totals from items
        $subtotal = 0;
        $totalQuantity = 0;
        $totalWeight = 0;
        $orderItemsData = [];

        foreach ($orderData['items'] as $itemData) {
            $product = $products->firstWhere('sku', $itemData['sku']);

            if ($product === null) {
                continue;
            }

            $unitPrice = (float) ($product->special_price ?? $product->base_price);
            $rowTotal = $unitPrice * $itemData['quantity'];
            $subtotal += $rowTotal;
            $totalQuantity += $itemData['quantity'];
            $totalWeight += (float) $product->weight * $itemData['quantity'];

            $orderItemsData[] = [
                'product' => $product,
                'quantity' => $itemData['quantity'],
                'unit_price' => $unitPrice,
                'row_total' => $rowTotal,
            ];
        }

        $shippingAmount = (float) $orderData['shipping_amount'];
        $discountAmount = 0;

        if (isset($orderData['coupon_code'])) {
            $discountAmount = round($subtotal * 0.1, 4);
        }

        $taxAmount = round($subtotal * 0.08875, 4);
        $grandTotal = $subtotal + $shippingAmount + $taxAmount - $discountAmount;
        $totalPaid = in_array($orderData['status'], [OrderStatus::Completed, OrderStatus::Shipped, OrderStatus::Delivered, OrderStatus::Processing, OrderStatus::Refunded])
            ? $grandTotal
            : 0;
        $totalRefunded = $orderData['status'] === OrderStatus::Refunded ? $grandTotal : 0;

        $order = Order::create([
            'increment_id' => $incrementId,
            'store_id' => $store->id,
            'customer_id' => $customer->id,
            'customer_email' => $customer->email,
            'customer_group_id' => $customer->customer_group_id,
            'status' => $orderData['status'],
            'subtotal' => $subtotal,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'total_paid' => $totalPaid,
            'total_refunded' => $totalRefunded,
            'currency_code' => 'USD',
            'shipping_method_code' => $orderData['shipping_method_code'],
            'shipping_method_label' => $orderData['shipping_method_label'],
            'payment_method_code' => $orderData['payment_method_code'],
            'payment_method_label' => $orderData['payment_method_label'],
            'coupon_code' => $orderData['coupon_code'] ?? null,
            'total_quantity' => $totalQuantity,
            'weight' => $totalWeight,
            'admin_note' => $orderData['admin_note'] ?? null,
            'ip_address' => '192.168.1.'.rand(1, 254),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        // Create order items
        $createdOrderItems = [];

        foreach ($orderItemsData as $itemData) {
            $taxRate = 8.875;
            $itemTax = round($itemData['row_total'] * $taxRate / 100, 4);

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $itemData['product']->id,
                'product_uuid' => $itemData['product']->uuid,
                'product_type' => $itemData['product']->type->value,
                'sku' => $itemData['product']->sku,
                'name' => $itemData['product']->name,
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'row_total' => $itemData['row_total'],
                'discount_amount' => 0,
                'tax_amount' => $itemTax,
                'tax_rate' => $taxRate,
                'weight' => $itemData['product']->weight,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $createdOrderItems[] = $orderItem;
        }

        // Create order addresses
        $defaultAddress = CustomerAddress::where('customer_id', $customer->id)
            ->where('is_default_shipping', true)
            ->first();

        if ($defaultAddress !== null) {
            $country = Country::where('iso2', $defaultAddress->country_id)->first();

            foreach ([OrderAddressType::Billing, OrderAddressType::Shipping] as $addressType) {
                OrderAddress::create([
                    'order_id' => $order->id,
                    'type' => $addressType,
                    'first_name' => $defaultAddress->first_name,
                    'last_name' => $defaultAddress->last_name,
                    'company' => $defaultAddress->company,
                    'street_line_1' => $defaultAddress->street_line_1,
                    'street_line_2' => $defaultAddress->street_line_2,
                    'city' => $defaultAddress->city,
                    'region_id' => $defaultAddress->region_id,
                    'postcode' => $defaultAddress->postcode,
                    'country_id' => $country?->id,
                    'country_name' => $country?->name ?? '',
                    'phone' => $defaultAddress->phone,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        // Create order history
        $this->createOrderHistory($order, $orderData['status'], $createdAt);

        // Create invoice if applicable
        if ($orderData['has_invoice']) {
            $this->createInvoice($order, $createdOrderItems, $createdAt);
        }
    }

    private function createOrderHistory(Order $order, OrderStatus $finalStatus, Carbon $createdAt): void
    {
        $statusFlow = [
            OrderStatus::Pending->value => ['Pending'],
            OrderStatus::Processing->value => ['Pending', 'Processing'],
            OrderStatus::OnHold->value => ['Pending', 'On Hold'],
            OrderStatus::Shipped->value => ['Pending', 'Processing', 'Shipped'],
            OrderStatus::Delivered->value => ['Pending', 'Processing', 'Shipped', 'Delivered'],
            OrderStatus::Completed->value => ['Pending', 'Processing', 'Shipped', 'Delivered', 'Completed'],
            OrderStatus::Canceled->value => ['Pending', 'Canceled'],
            OrderStatus::Refunded->value => ['Pending', 'Processing', 'Shipped', 'Delivered', 'Refunded'],
            OrderStatus::Returned->value => ['Pending', 'Processing', 'Shipped', 'Delivered', 'Returned'],
        ];

        $statusComments = [
            'Pending' => 'Order placed.',
            'Processing' => 'Payment confirmed. Order is being processed.',
            'On Hold' => 'Order placed on hold.',
            'Shipped' => 'Order has been shipped.',
            'Delivered' => 'Order delivered to customer.',
            'Completed' => 'Order completed.',
            'Canceled' => 'Order has been canceled.',
            'Refunded' => 'Order has been refunded.',
            'Returned' => 'Order has been returned.',
        ];

        $flow = $statusFlow[$finalStatus->value] ?? ['Pending'];
        $hoursOffset = 0;

        foreach ($flow as $statusLabel) {
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => strtolower(str_replace(' ', '_', $statusLabel)),
                'comment' => $statusComments[$statusLabel] ?? null,
                'is_customer_notified' => true,
                'created_at' => $createdAt->copy()->addHours($hoursOffset),
            ]);

            $hoursOffset += rand(2, 48);
        }
    }

    /**
     * @param  array<int, OrderItem>  $orderItems
     */
    private function createInvoice(Order $order, array $orderItems, Carbon $createdAt): void
    {
        $this->invoiceCounter++;
        $invoiceIncrementId = '2000'.str_pad((string) $this->invoiceCounter, 5, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'increment_id' => $invoiceIncrementId,
            'status' => InvoiceStatus::Paid,
            'subtotal' => $order->subtotal,
            'shipping_amount' => $order->shipping_amount,
            'discount_amount' => $order->discount_amount,
            'tax_amount' => $order->tax_amount,
            'grand_total' => $order->grand_total,
            'created_at' => $createdAt->copy()->addHours(1),
            'updated_at' => $createdAt->copy()->addHours(1),
        ]);

        foreach ($orderItems as $orderItem) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'order_item_id' => $orderItem->id,
                'quantity' => $orderItem->quantity,
                'row_total' => $orderItem->row_total,
                'tax_amount' => $orderItem->tax_amount,
            ]);
        }
    }
}
