# Directory Module

**Tier:** 1 — Foundation
**Dependencies:** Store

## Responsibility

Reference/lookup data used across the platform: countries, regions, currencies.

## Domain

- **Countries** — list of countries with ISO codes, enabled/disabled per store
- **Regions** — states/provinces/oblasts per country
- **Currencies** — supported currencies, exchange rates, formatting rules
- **Units** — weight units (kg, lb), dimension units (cm, in)

## Key Entities

- `Country`
- `Region`
- `Currency`
- `CurrencyRate`

## Facades

- `CountryFacade` — list countries, get regions by country, check availability per store
- `CurrencyFacade` — convert amounts, get current currency, format money
