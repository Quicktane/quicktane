# Notification Module

**Tier:** 4 — Content & Search
**Dependencies:** Store

## Responsibility

Centralized notification system. All outgoing communications (email, future SMS/push)
go through this module. Other modules use NotificationFacade or dispatch events
that Notification listeners handle.

## Domain

- **Email Templates** — Blade-based templates for transactional emails (order confirmation, shipping update, password reset, welcome email)
- **Email Sending** — queue-based email dispatch via Laravel Mail
- **Template Variables** — each template defines required variables, modules provide data via events
- **Notification Log** — history of sent notifications (who, what, when, status)
- **Channels** — extensible: email now, SMS and push notifications later

## Key Entities

- `EmailTemplate`
- `NotificationLog`
- `NotificationChannel` (enum: email, sms, push)

## Facades

- `NotificationFacade` — send notification by template + data, queue notification, get send history

## Integration Pattern

Other modules do NOT send emails directly. They dispatch After-events,
and Notification module listens:

```
AfterOrderPlace → SendOrderConfirmationListener (in Notification module)
AfterCustomerRegister → SendWelcomeEmailListener (in Notification module)
AfterOrderStatusChange → SendStatusUpdateListener (in Notification module)
```

This keeps email logic centralized and templates manageable.
