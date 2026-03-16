# CMS Module

**Tier:** 4 — Content & Search
**Dependencies:** Store, Media

## Responsibility

Static content management: pages, reusable blocks, and URL management.

## Domain

- **Pages** — static content pages (About Us, Terms & Conditions, FAQ, Contact)
- **Blocks** — reusable content snippets (promo banner, footer links, announcement bar)
- **URL Rewrites** — SEO-friendly URLs, redirects (301/302)
- **Slugs** — unique URL identifiers per page/entity, per store view

## Key Entities

- `Page`
- `Block`
- `UrlRewrite`

## Facades

- `PageFacade` — get page by slug, list pages, get page content
- `BlockFacade` — get block by identifier, render block content
- `UrlFacade` — resolve URL to entity, generate SEO URL, manage redirects
