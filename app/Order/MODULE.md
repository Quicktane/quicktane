# Order Module

**Tier:** 3 — Purchase Flow
**Dependencies:** Customer, Catalog

## Responsibility

Order lifecycle management with state machine, invoices, credit memos, and shipment records.

## Domain

- **Orders** — placed orders with items, addresses, totals, payment/shipping info
- **OrderItems** — line items with product snapshot (price, name, options at time of purchase)
- **State Machine** — order status transitions with Before/After events
  - States: pending → processing → shipped → delivered → completed
  - Also: on_hold, canceled, returned, refunded
  - See State Machine section below
- **Invoices** — billing documents generated on payment capture
- **Credit Memos** — refund documents with line items and amounts
- **Order History** — timeline of all status changes, comments, actions

## Key Entities

- `Order`
- `OrderItem`
- `OrderAddress`
- `OrderStatus` (enum)
- `Invoice`
- `InvoiceItem`
- `CreditMemo`
- `CreditMemoItem`
- `OrderHistory`

## Facades

- `OrderFacade` — create order, get order by id/uuid, get orders by customer
- `OrderStatusFacade` — change status (via state machine), get allowed transitions
- `InvoiceFacade` — create invoice, get invoice by order
- `CreditMemoFacade` — create credit memo, process refund

## State Machine

Order lifecycle is controlled by a state machine. Each transition dispatches
Before/After events, allowing other modules to hook into status changes.

### States and transitions
```
                    ┌──────────┐
                    │ pending  │ ← initial state after order.place
                    └────┬─────┘
                         │
              ┌──────────┼──────────┐
              ▼          ▼          ▼
         ┌────────┐ ┌──────────┐ ┌──────────┐
         │ on_hold│ │processing│ │ canceled │
         └───┬────┘ └────┬─────┘ └──────────┘
             │           │
             │     ┌─────┼──────────┐
             │     ▼     ▼          ▼
             │ ┌───────┐ ┌────────┐ ┌──────────┐
             └▶│process.│ │ shipped│ │ canceled │
               └───────┘ └───┬────┘ └──────────┘
                              │
                        ┌─────┼─────┐
                        ▼           ▼
                   ┌──────────┐ ┌──────────┐
                   │ delivered│ │ returned │
                   └────┬─────┘ └──────────┘
                        │
                        ▼
                   ┌──────────┐
                   │ completed│
                   └──────────┘

Refund can happen from: processing, shipped, delivered, completed
                   ┌──────────┐
                   │ refunded │
                   └──────────┘
```

### Allowed transitions
```
pending     → processing, on_hold, canceled
on_hold     → processing, canceled
processing  → shipped, canceled, refunded
shipped     → delivered, returned
delivered   → completed, refunded
completed   → refunded
returned    → refunded
```

### Transition events
Every status change dispatches:
- `BeforeOrderStatusChange` — sync, can block (e.g., prevent cancellation if already shipped)
- `AfterOrderStatusChange` — sync/async per listener

```php
class BeforeOrderStatusChange
{
    public function __construct(
        public readonly Order $order,
        public readonly string $fromStatus,
        public readonly string $toStatus,
        public readonly OperationContext $context,
    ) {}
}
```

### Rules
- Status can ONLY change via `OrderService::changeStatus()` — never by setting model attribute directly
- Invalid transitions throw `InvalidOrderTransitionException`
- Transition logic lives in `app/Modules/Order/StateMachine/OrderStateMachine.php`
- States are defined as an enum: `app/Modules/Order/Enums/OrderStatus.php`
