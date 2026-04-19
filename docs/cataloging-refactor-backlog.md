# Cataloging refactor backlog

## Salvage milestone track

- Preserve `/api/category/*` routes during internal hardening.
- Extract request normalization into reusable support helpers.
- Centralize mutation request actor/idempotency/correlation resolution.
- Replace string-based not-found heuristics with explicit exceptions.
- Grow mutation regression coverage for idempotency replay and deep-tree rebasing.
- Defer public `CatalogCategory*` route migration until post-green contract review.
