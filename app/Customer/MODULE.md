# Customer Module

**Tier:** 2 — Core Commerce
**Dependencies:** Store, Directory

## Responsibility

Customer accounts, addresses, groups, and segmentation.

## Domain

- **Customers** — storefront user accounts (registration, login, profile)
- **Authentication** — Sanctum-based, separate guard from admin users
- **Addresses** — address book with billing/shipping designation (linked to Directory for country/region)
- **Customer Groups** — classification that affects pricing, tax, and promotions (Retail, Wholesale, VIP)
- **Customer Segments** — dynamic grouping based on behavior/attributes (spent > $1000, registered > 1 year) for targeted promotions

## Key Entities

- `Customer`
- `CustomerAddress`
- `CustomerGroup`
- `CustomerSegment`

## Facades

- `CustomerFacade` — find customer, get customer with addresses, check group membership
- `CustomerGroupFacade` — get group by customer, list groups, check segment membership
