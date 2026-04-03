# Category Search Readiness

## Goal

Promote category search from demo-grade in-memory behavior to projection-backed SQL reads.

## Current baseline

- Search reads from `category_projection`
- Search exposes filters for `q`, `tenant`, `locale`, `workflow_state`, `published`
- Search supports `limit`, `offset`, `order`, and `direction`
- Search no longer writes facet stats to local report files during request handling
- Projection schema includes search-oriented indexes for `name`, `slug`, `tenant+locale`, `workflow_state`, and `updated_at`

## Remaining considerations

- Current implementation uses `LIKE` semantics rather than full-text indexing
- Facets are computed from the same filtered projection scope
- Projection freshness still depends on outbox/projection processing health

## Readiness status

Search/read readiness is considered `pass` when:

1. Search is projection-backed rather than in-memory
2. Search has no file-based request side effects
3. Search contract is documented in canonical and legacy OpenAPI files
4. Search-oriented projection indexes exist in schema and migration history
5. Search readiness is included in the RC aggregate
