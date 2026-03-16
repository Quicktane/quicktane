# User Module

**Tier:** 1 — Foundation
**Dependencies:** none (standalone)

## Responsibility

Admin user management, authentication, roles, and permissions (ACL).
This is NOT for customers — customers have their own module.

## Domain

- **Admin Users** — admin panel accounts (email/password, Sanctum tokens)
- **Roles** — named permission groups (Super Admin, Catalog Manager, Order Viewer)
- **Permissions** — granular actions (catalog.product.create, order.view, order.refund)
- **ACL** — role-based access control for admin API routes

## Key Entities

- `AdminUser`
- `Role`
- `Permission`

## Facades

- `AuthFacade` — authenticate admin, check permissions, get current admin user
- `AclFacade` — check if user/role has permission, list permissions
