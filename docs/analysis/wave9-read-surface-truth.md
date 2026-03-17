# Wave 9 - read surface truth

## Scope
- storefront read controller
- store API read controller
- search controller envelope and facet truth
- read optimizer fixture realism for locale/channel/published seams

## What improved
- storefront now returns a canonical envelope with `data`, `channel`, `locale`, and `count`
- storefront excludes unpublished rows by default and supports locale filtering
- store API returns a stable envelope and locale/channel-aware filtering
- search controller now returns canonical `query`, `count`, `items`, and `facets` payload instead of a raw service dump

## Why this matters
This wave tightens the read-side surface so that storefront and store API paths are less decorative and closer to stable application contracts.

## Remaining gap
- `CategoryReadController` still depends on Doctrine/query-builder seams and is not yet covered by direct truth-tests
- read-side persistence is still synthetic rather than adapter-backed
