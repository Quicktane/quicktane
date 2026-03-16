# Search Module

**Tier:** 4 — Content & Search
**Dependencies:** Catalog, Inventory

## Responsibility

Product search, indexing, and faceted filtering via Meilisearch or Elasticsearch.

## Domain

- **Search Indexing** — sync product data to search engine (product name, description, attributes, price, stock status)
- **Full-Text Search** — keyword search with relevance ranking, typo tolerance
- **Faceted Filtering** — filter by attributes (color, size), price range, category, stock status
- **Autocomplete** — search suggestions as user types
- **Sort** — by relevance, price, name, newest, bestselling
- **Reindexing** — full and partial reindex, triggered by product/inventory changes via events

## Key Entities

- `SearchIndex`
- `SearchSynonym`
- `SearchFilter`

## Facades

- `SearchFacade` — search products, get facets/filters, autocomplete suggestions
- `IndexFacade` — trigger reindex, get index status, manage synonyms
