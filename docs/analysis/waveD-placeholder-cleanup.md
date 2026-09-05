# Wave D — Synthetic Residue Cleanup

This wave was executed strictly from the current repository snapshot.

## Touched runtime seams

- `src/Observability/PrometheusController.php`
  - now exposes `category_projection_lag_seconds` from `CatalogProjectionMetrics`
- `src/Idempotency/CategoryIdempotencyStore.php`
  - process-local fallback now purges expired entries instead of acting as an unbounded synthetic seam
- `src/Worker/ProjectionSyncWorker.php`
  - delegates to `CategoryProjectionRunnerInterface` when wired, instead of staying comment-only
- `src/Runner/CategoryProjectionRunner.php`
  - emits deterministic `projection.tick` payloads instead of a raw `noop`

## Intentionally not touched in this wave

The following areas still contain synthetic seams and require a separate fact-based wave:

- GraphQL query synthetic seam collections
- repository methods returning empty arrays for not-yet-wired branches
- attachment / DLQ / collection-rule services with fallback empty returns
- security OIDC synthetic seam signing

## Outcome

This wave reduces low-value synthetic seam residue without inventing new architecture or changing repository boundaries.
