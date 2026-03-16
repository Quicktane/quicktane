<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Database\Seeders;

use Illuminate\Database\Seeder;
use Quicktane\Promotion\Enums\ActionType;
use Quicktane\Promotion\Models\CartPriceRule;
use Quicktane\Promotion\Models\Coupon;

class SamplePromotionSeeder extends Seeder
{
    public function run(): void
    {
        // 10% off with coupon
        $summerSale = CartPriceRule::firstOrCreate(
            ['name' => 'Summer Sale 10% Off'],
            [
                'description' => 'Get 10% off your entire order during our summer sale event.',
                'is_active' => true,
                'from_date' => '2026-06-01',
                'to_date' => '2026-08-31',
                'priority' => 0,
                'stop_further_processing' => false,
                'action_type' => ActionType::ByPercent,
                'action_amount' => 10.00,
                'apply_to_shipping' => false,
                'times_used' => 0,
                'sort_order' => 0,
            ],
        );

        Coupon::firstOrCreate(
            ['code' => 'SUMMER10'],
            [
                'cart_price_rule_id' => $summerSale->id,
                'usage_limit' => 1000,
                'usage_per_customer' => 1,
                'times_used' => 42,
                'is_active' => true,
                'expires_at' => '2026-08-31 23:59:59',
            ],
        );

        // $15 off orders over $100
        $bigOrderDiscount = CartPriceRule::firstOrCreate(
            ['name' => '$15 Off Orders Over $100'],
            [
                'description' => 'Save $15 on any order of $100 or more.',
                'is_active' => true,
                'priority' => 1,
                'stop_further_processing' => false,
                'action_type' => ActionType::ByFixed,
                'action_amount' => 15.00,
                'apply_to_shipping' => false,
                'times_used' => 0,
                'sort_order' => 1,
            ],
        );

        Coupon::firstOrCreate(
            ['code' => 'SAVE15'],
            [
                'cart_price_rule_id' => $bigOrderDiscount->id,
                'usage_limit' => 500,
                'usage_per_customer' => 3,
                'times_used' => 18,
                'is_active' => true,
            ],
        );

        // Free shipping promotion
        $freeShippingPromo = CartPriceRule::firstOrCreate(
            ['name' => 'Free Shipping Weekend'],
            [
                'description' => 'Free shipping on all orders this weekend.',
                'is_active' => false,
                'priority' => 2,
                'stop_further_processing' => true,
                'action_type' => ActionType::FreeShipping,
                'action_amount' => 0.00,
                'apply_to_shipping' => true,
                'times_used' => 0,
                'sort_order' => 2,
            ],
        );

        Coupon::firstOrCreate(
            ['code' => 'FREESHIP'],
            [
                'cart_price_rule_id' => $freeShippingPromo->id,
                'usage_limit' => null,
                'usage_per_customer' => 1,
                'times_used' => 0,
                'is_active' => false,
            ],
        );

        // Welcome discount for new customers
        $welcomeDiscount = CartPriceRule::firstOrCreate(
            ['name' => 'Welcome Discount 20% Off'],
            [
                'description' => 'Welcome! Enjoy 20% off your first order.',
                'is_active' => true,
                'priority' => 0,
                'stop_further_processing' => true,
                'action_type' => ActionType::ByPercent,
                'action_amount' => 20.00,
                'max_discount_amount' => 50.00,
                'apply_to_shipping' => false,
                'times_used' => 0,
                'sort_order' => 3,
            ],
        );

        Coupon::firstOrCreate(
            ['code' => 'WELCOME20'],
            [
                'cart_price_rule_id' => $welcomeDiscount->id,
                'usage_limit' => null,
                'usage_per_customer' => 1,
                'times_used' => 156,
                'is_active' => true,
            ],
        );
    }
}
