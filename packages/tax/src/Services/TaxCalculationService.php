<?php

declare(strict_types=1);

namespace Quicktane\Tax\Services;

use Quicktane\Tax\DataTransferObjects\CartTaxResult;
use Quicktane\Tax\DataTransferObjects\TaxCalculationResult;
use Quicktane\Tax\Enums\TaxClassType;
use Quicktane\Tax\Models\TaxClass;
use Quicktane\Tax\Repositories\TaxClassRepository;
use Quicktane\Tax\Repositories\TaxRuleRepository;

class TaxCalculationService
{
    public function __construct(
        private readonly TaxZoneResolver $taxZoneResolver,
        private readonly TaxRuleRepository $taxRuleRepository,
        private readonly TaxClassRepository $taxClassRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $address
     */
    public function calculateTax(string $amount, int $productTaxClassId, int $customerTaxClassId, array $address): TaxCalculationResult
    {
        $taxZone = $this->taxZoneResolver->resolve($address);

        if ($taxZone === null) {
            return new TaxCalculationResult(
                taxAmount: '0.0000',
                rate: '0.0000',
                breakdown: [],
            );
        }

        $taxRules = $this->taxRuleRepository->findActiveByTaxClasses($productTaxClassId, $customerTaxClassId);

        $totalTax = '0.0000';
        $totalRate = '0.0000';
        $breakdown = [];
        $taxableAmount = $amount;

        $rulesByPriority = $taxRules
            ->filter(fn ($taxRule) => $taxRule->taxRate->tax_zone_id === $taxZone->id && $taxRule->taxRate->is_active)
            ->sortBy(fn ($taxRule) => $taxRule->taxRate->priority);

        foreach ($rulesByPriority as $taxRule) {
            $taxRate = $taxRule->taxRate;

            if ($taxRate->is_compound) {
                $taxableAmount = bcadd($amount, $totalTax, 4);
            }

            $taxAmount = bcdiv(bcmul($taxableAmount, $taxRate->rate, 8), '100', 4);
            $totalTax = bcadd($totalTax, $taxAmount, 4);
            $totalRate = bcadd($totalRate, $taxRate->rate, 4);
            $breakdown[$taxRate->name] = $taxAmount;
        }

        return new TaxCalculationResult(
            taxAmount: $totalTax,
            rate: $totalRate,
            breakdown: $breakdown,
        );
    }

    /**
     * @param  array<string, mixed>  $shippingAddress
     * @param  array<int, array<string, mixed>>  $cartItems
     */
    public function calculateCartTaxFromItems(array $cartItems, array $shippingAddress, ?int $customerTaxClassId = null): CartTaxResult
    {
        $defaultCustomerTaxClass = $customerTaxClassId ?? $this->getDefaultCustomerTaxClassId();
        $totalTax = '0.0000';
        $itemTaxes = [];

        foreach ($cartItems as $cartItem) {
            $productTaxClassId = (int) ($cartItem['tax_class_id'] ?? 0);

            if ($productTaxClassId === 0) {
                $itemTaxes[$cartItem['cart_item_id']] = '0.0000';

                continue;
            }

            $result = $this->calculateTax(
                $cartItem['row_total'],
                $productTaxClassId,
                $defaultCustomerTaxClass,
                $shippingAddress,
            );

            $itemTaxes[$cartItem['cart_item_id']] = $result->taxAmount;
            $totalTax = bcadd($totalTax, $result->taxAmount, 4);
        }

        return new CartTaxResult(
            totalTax: $totalTax,
            itemTaxes: $itemTaxes,
        );
    }

    /**
     * @param  array<string, mixed>  $address
     */
    public function getApplicableRate(int $productTaxClassId, int $customerTaxClassId, array $address): string
    {
        $taxZone = $this->taxZoneResolver->resolve($address);

        if ($taxZone === null) {
            return '0.0000';
        }

        $taxRules = $this->taxRuleRepository->findActiveByTaxClasses($productTaxClassId, $customerTaxClassId);

        $totalRate = '0.0000';

        foreach ($taxRules as $taxRule) {
            $taxRate = $taxRule->taxRate;

            if ($taxRate->tax_zone_id === $taxZone->id && $taxRate->is_active) {
                $totalRate = bcadd($totalRate, $taxRate->rate, 4);
            }
        }

        return $totalRate;
    }

    public function getDefaultProductTaxClass(): ?TaxClass
    {
        return $this->taxClassRepository->findDefault(TaxClassType::Product);
    }

    public function getDefaultCustomerTaxClass(): ?TaxClass
    {
        return $this->taxClassRepository->findDefault(TaxClassType::Customer);
    }

    private function getDefaultCustomerTaxClassId(): int
    {
        $defaultClass = $this->getDefaultCustomerTaxClass();

        return $defaultClass?->id ?? 0;
    }
}
