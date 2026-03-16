# Cart Module

**Tier:** 3 — Purchase Flow
**Dependencies:** Catalog, Customer, Inventory

## Responsibility

Shopping cart management for guests and authenticated customers.

## Domain

- **Cart** — shopping cart entity, one active cart per customer/guest session
- **CartItems** — products added to cart with quantity, resolved price, options (size, color)
- **Guest Cart** — identified by token, converted to customer cart on login
- **Cart Merge** — when guest logs in, merge guest cart items into existing customer cart
- **Cart Validation** — verify items are still in stock, prices haven't changed, products still active

## Key Entities

- `Cart`
- `CartItem`

## Facades

- `CartFacade` — get active cart, add/remove/update items, get cart totals, clear cart, revalidate prices

## Price Locking

Prices can change between "add to cart" and "place order". Cart snapshots prices
and Checkout re-validates them.

### Flow
1. **Add to cart** — snapshot current price into cart item (`unit_price`, `snapshotted_at`)
2. **Checkout start** — `CartFacade::revalidatePrices()` checks all prices against current catalog
3. **Price changed** — notify customer, update cart with new prices, require re-confirmation
4. **Place order** — use validated cart prices (already confirmed by customer)

### Rules
- Cart items store `unit_price` and `snapshotted_at` — the price at time of adding
- Price changes do NOT silently update — customer must see and confirm
- Order stores final prices — no further price lookups after order creation
