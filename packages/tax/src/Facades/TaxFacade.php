<?php

declare(strict_types=1);

namespace Quicktane\Tax\Facades;

use App\Cart\Contracts\CartFacade as CartFacadeContract;
use App\Catalog\Contracts\ProductFacade as ProductFacadeContract;
use Quicktane\Tax\Contracts\TaxFacade as TaxFacadeContract;
use Quicktane\Tax\DataTransferObjects\CartTaxResult;
use Quicktane\Tax\DataTransferObjects\TaxCalculationResult;
use Quicktane\Tax\Models\TaxClass;
use Quicktane\Tax\Repositories\TaxClassRepository;
use Quicktane\Tax\Services\TaxCalculationService;

class TaxFacade implements TaxFacadeContract
{
    public function __construct(
        private readonly TaxCalculationService $taxCalculationService,
        private readonly TaxClassRepository $taxClassRepository,
        private readonly CartFacadeContract $cartFacade,
        private readonly ProductFacadeContract $productFacade,
    ) {}

    public function calculateTax(string $amount, int $productTaxClassId, int $customerTaxClassId, array $address): TaxCalculationResult
    {
        return $this->taxCalculationService->calculateTax($amount, $productTaxClassId, $customerTaxClassId, $address);
    }

    public function calculateCartTax(int $cartId, array $shippingAddress, ?int $customerTaxClassId = null): CartTaxResult
    {
        $cart = $this->cartFacade->getCartWithItems($cartId);

        if ($cart === null) {
            return new CartTaxResult(totalTax: '0.0000', itemTaxes: []);
        }

        $cartItems = [];

        foreach ($cart->items as $cartItem) {
            $product = $this->productFacade->getProduct($cartItem->product_uuid);

            $cartItems[] = [
                'cart_item_id' => $cartItem->id,
                'row_total' => $cartItem->row_total,
                'tax_class_id' => $product?->tax_class_id ?? 0,
            ];
        }

        return $this->taxCalculationService->calculateCartTaxFromItems($cartItems, $shippingAddress, $customerTaxClassId);
    }

    public function getApplicableRate(int $productTaxClassId, int $customerTaxClassId, array $address): string
    {
        return $this->taxCalculationService->getApplicableRate($productTaxClassId, $customerTaxClassId, $address);
    }

    public function getTaxClass(int $id): ?TaxClass
    {
        return $this->taxClassRepository->findById($id);
    }

    public function getDefaultProductTaxClass(): ?TaxClass
    {
        return $this->taxCalculationService->getDefaultProductTaxClass();
    }

    public function getDefaultCustomerTaxClass(): ?TaxClass
    {
        return $this->taxCalculationService->getDefaultCustomerTaxClass();
    }
}
