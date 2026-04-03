# Category RC readiness

This repository exposes a machine-readable RC readiness snapshot.

## Refresh sequence

```bash
php tools/inspection/CatalogRuntimeProofReport.php
php tools/inspection/CatalogSmokeProofReport.php
php tools/inspection/CatalogRouteInventoryReport.php
php tools/inspection/CatalogDependencyBaselineReport.php
php tools/inspection/CatalogApiContractReadinessReport.php
php tools/inspection/CatalogSecurityReadinessReport.php
php tools/inspection/CatalogOidcRuntimeProofReport.php
php tools/inspection/CatalogOwnerOverlapReport.php
php tools/inspection/CatalogClassAliasReport.php
php tools/inspection/CatalogRcReadinessReport.php
```

Or through Composer:

```bash
composer report:runtime-proof
composer report:smoke-proof
composer report:route-inventory
composer report:dependency-baseline
composer report:api-contract-readiness
composer report:security-readiness
composer report:oidc-runtime-proof
composer report:owner-overlap
composer report:class-alias
composer report:rc-readiness
```

Generated artifacts are written to `report/inspection/`.

## RC gate model

The readiness report currently evaluates these RC-facing gates:

- git working tree cleanliness
- `APP_ENV=prod APP_DEBUG=0 php bin/console about`
- runtime proof artifact presence
- smoke proof report (container/runtime/routes/doctrine/fixtures/prod console)
- route inventory availability
- bundle loadability from `config/catalog_bundles.php`
- dependency baseline cleanliness
- PHPUnit extension readiness
- owner-overlap signals
- duplicate class-basename signals
- API contract readiness (OpenAPI + router + Nelmio + version alignment)
- security readiness (firewall + protected write/admin surfaces + OIDC/JWKS artifacts)
- OIDC runtime proof (JWKS-backed signature, issuer/audience, fail-closed validator behavior)

## Status meanings

- `pass`: acceptable for RC conditioning
- `warn`: not a hard runtime blocker, but still below release confidence
- `fail`: immediate RC blocker

## Typical warning-class items

- missing local PHPUnit extensions in stripped environments (`dom`, `mbstring`, `xml`, `xmlwriter`)
- remaining owner-overlap/class-alias signals during catalog/category convergence

These items are now explicit and machine-readable instead of remaining implicit tribal knowledge.

## Additional RC gate
- outbox / projection readiness must pass before declaring write/read pipeline ready.

- idempotency readiness should stay at `pass` before RC is treated as production-candidate clean.

- search-readiness

php tools/inspection/CatalogExternalBoundaryReadinessReport.php
