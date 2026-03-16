# Tax Module

**Tier:** 3 — Purchase Flow
**Dependencies:** Store, Directory, Catalog

## Responsibility

Tax calculation, rules, rates, and zone management.

## Domain

- **Tax Rates** — percentage rates tied to geographic zones (Ukraine VAT 20%, US CA Sales Tax 7.25%)
- **Tax Zones** — geographic areas where a rate applies (country, country+region, zip range)
- **Tax Rules** — link tax rates to product tax classes and customer tax classes
- **Tax Classes** — classification for products (Taxable Goods, Food, Digital, Tax Exempt) and customers (Retail, Wholesale, Tax Exempt)
- **Tax Calculation** — price inclusive vs exclusive tax, rounding rules, compound tax
- **Tax Display** — how tax is shown (included in price, excluded, both)

## Key Entities

- `TaxRate`
- `TaxZone`
- `TaxRule`
- `TaxClass`

## Facades

- `TaxFacade` — calculate tax for product/cart, get applicable rate for address, resolve tax class
