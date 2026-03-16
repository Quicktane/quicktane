# Store Module

**Tier:** 1 — Foundation
**Dependencies:** none (base module)

## Responsibility

Multi-store configuration and scoping. Everything in the system is scoped to a store.

## Domain

- **Websites** — top-level grouping (e.g., B2C website, B2B website)
- **Stores** — store groups within a website (e.g., "Clothing Store", "Electronics Store")
- **StoreViews** — language/locale variants of a store (e.g., "English", "Ukrainian")
- **Configuration** — global and per-store settings (store name, default currency, timezone, locale)
- **Scoping** — mechanism to resolve config values: StoreView → Store → Website → Global

## Key Entities

- `Website`
- `Store`
- `StoreView`
- `Configuration`

## Facades

- `StoreFacade` — resolve current store, get store config, list stores/websites
- `ConfigurationFacade` — get/set config values with scope resolution
