# Checkout Module

**Tier:** 3 — Purchase Flow
**Dependencies:** Cart, Promotion, Tax, Shipping, Payment, Order, Customer

## Responsibility

Thin orchestrator for the checkout flow. Minimal own business logic —
coordinates other modules to complete a purchase.

## Domain

- **Checkout Flow** — step-by-step process: shipping address → shipping method → payment method → place order
- **Totals Calculation** — orchestrates totals pipeline (subtotal, shipping, discount, tax, grand total) — will use Totals Pipeline from backlog
- **Order Placement** — the main operation: validate cart → calculate totals → authorize payment → reserve inventory → create order
- **Checkout Session** — temporary state during checkout (selected shipping method, payment method, addresses)

## Key Entities

- `CheckoutSession`

## Facades

- `CheckoutFacade` — start checkout, set shipping/payment method, place order, get totals

## Price Revalidation at Checkout

ValidateCartStep in the checkout pipeline re-validates all cart prices before proceeding.
If prices changed since the customer added items, checkout is interrupted.

```php
class ValidateCartStep implements PipelineStep
{
    public static bool $requiresRevalidation = true;

    public function handle(PipelineContext $context, Closure $next): mixed
    {
        $cart = $context->get('cart');
        $priceChanges = $this->cartFacade->revalidatePrices($cart);

        if ($priceChanges->hasChanges()) {
            $context->set('price_changes', $priceChanges);
            throw new PriceChangedException($priceChanges);
            // Controller catches → returns changes to frontend → customer confirms
        }

        return $next($context);
    }
}
```

- Steps with `$requiresRevalidation = true` re-run on pipeline resume (prices may change during 3DS)
- Price snapshot lives in Cart module; revalidation is triggered by Checkout
