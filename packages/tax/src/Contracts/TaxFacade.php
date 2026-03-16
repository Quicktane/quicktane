<?php

declare(strict_types=1);

namespace Quicktane\Tax\Contracts;

use Quicktane\Tax\DataTransferObjects\CartTaxResult;
use Quicktane\Tax\DataTransferObjects\TaxCalculationResult;
use Quicktane\Tax\Models\TaxClass;

interface TaxFacade
{
    /**
     * @param  array<string, mixed>  $address
     */
    public function calculateTax(string $amount, int $productTaxClassId, int $customerTaxClassId, array $address): TaxCalculationResult;

    /**
     * @param  array<string, mixed>  $shippingAddress
     */
    public function calculateCartTax(int $cartId, array $shippingAddress, ?int $customerTaxClassId = null): CartTaxResult;

    /**
     * @param  array<string, mixed>  $address
     */
    public function getApplicableRate(int $productTaxClassId, int $customerTaxClassId, array $address): string;

    public function getTaxClass(int $id): ?TaxClass;

    public function getDefaultProductTaxClass(): ?TaxClass;

    public function getDefaultCustomerTaxClass(): ?TaxClass;
}
