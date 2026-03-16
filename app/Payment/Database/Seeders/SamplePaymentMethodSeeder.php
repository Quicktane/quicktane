<?php

declare(strict_types=1);

namespace App\Payment\Database\Seeders;

use App\Payment\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class SamplePaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'code' => 'credit_card',
                'name' => 'Credit Card',
                'gateway_code' => 'offline',
                'description' => 'Pay securely with Visa, Mastercard, or American Express',
                'is_active' => true,
                'sort_order' => 0,
            ],
            [
                'code' => 'paypal',
                'name' => 'PayPal',
                'gateway_code' => 'offline',
                'description' => 'Pay with your PayPal account',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'bank_transfer',
                'name' => 'Bank Transfer',
                'gateway_code' => 'offline',
                'description' => 'Pay via direct bank transfer. Your order will be processed after payment is received.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'cash_on_delivery',
                'name' => 'Cash on Delivery',
                'gateway_code' => 'offline',
                'description' => 'Pay with cash when your order is delivered',
                'is_active' => true,
                'sort_order' => 3,
                'max_order_amount' => 500.00,
            ],
        ];

        foreach ($paymentMethods as $methodData) {
            PaymentMethod::firstOrCreate(
                ['code' => $methodData['code']],
                $methodData,
            );
        }
    }
}
