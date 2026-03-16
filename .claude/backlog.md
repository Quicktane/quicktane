# Architecture Backlog

Items discussed but not yet added to architecture-rules.md. Add when ready to implement.

## Totals Pipeline (critical)
- `core/Pricing/` — ordered pipeline for cart total calculation
- `TotalCollector` interface with `collect()` and `getOrder()` methods
- Each module registers its collector (Subtotal→Shipping→Discount→Tax→GrandTotal)
- Third-party modules insert their collectors at specific order positions (Loyalty, GiftCard, Insurance)

## Payment Method Registry (critical)
- `core/Payment/PaymentMethod` interface — `authorize()`, `capture()`, `void()`, `refund()`
- `PaymentMethodRegistry` — modules register payment methods via ServiceProvider
- Third-party modules create their own implementations (Stripe, PayPal, LiqPay)

## Shipping Method Registry (critical)
- `core/Shipping/ShippingMethod` interface — `calculateRate()`, `getEstimatedDays()`
- `ShippingMethodRegistry` — modules register shipping methods via ServiceProvider
- Third-party modules add carriers (NovaPoshta, UkrPoshta, DHL)

## Extension Data on Entities (important)
- Mechanism for third-party modules to attach extra data to core entities (Order, Product, Customer)
- Options: JSON `meta` column, separate EAV table, or FK-linked tables per module
- Needed for: loyalty_points_earned on Order, gift_card_code on Order, etc.

## Indexers (later)
- `core/Indexing/` — reindex prices, stock, categories into flat tables for fast search
- Needed for Search module performance
- Can be deferred until Search module is built

---

## Tier 5 Modules (build as needed or as third-party)

### Wishlist
- Customer wishlists with product items
- Dependencies: Catalog, Customer
- Guest wishlist (cookie-based) + merge on login

### Review
- Product reviews and star ratings
- Dependencies: Catalog, Customer
- Moderation (pending/approved/rejected)
- Review summary (avg rating, count) cached on product

### Compare
- Product comparison lists
- Dependencies: Catalog
- Compare by attributes within same attribute set

### Newsletter
- Email subscription management
- Dependencies: Customer, Notification
- Subscribe/unsubscribe, double opt-in
- Integration with email marketing platforms (Mailchimp, SendGrid)

### Report
- Sales reports, product performance, customer analytics
- Dependencies: Order, Catalog, Customer
- Aggregated data, date range filtering, export

### Import
- Bulk import/export for products, customers, orders
- Dependencies: Catalog, Customer, Order
- CSV/XML formats, validation, error reporting, async processing via queue

### Seo
- Meta tags management, sitemap.xml generation, structured data (JSON-LD)
- Dependencies: Catalog, CMS
- Auto-generate meta from product/category data, canonical URLs
