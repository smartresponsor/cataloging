# Category API contract readiness

This repository now exposes a machine-readable API contract readiness snapshot.

## Refresh

```bash
composer report:api-contract-readiness
```

Artifact:

- `report/inspection/catalog-api-contract-readiness-report.json`

## What it checks

- canonical OpenAPI contract exists in `api/catalog-openapi.yaml`
- legacy compatibility contract exists in `api/category-openapi.yaml`
- canonical documented paths are routable in the live Symfony router
- Nelmio documentation routes exist both in config and in runtime routing
- API version strings stay aligned across canonical OpenAPI, compatibility OpenAPI, and Nelmio docs
- API versioning documentation exists
- GraphQL contract surface remains present

## Why this matters

This turns API/contract readiness into an explicit RC signal instead of relying on scattered files and tribal knowledge.

The key architectural question is not only whether controllers exist. It is whether the published contract surface, runtime routing, and documentation endpoints stay aligned.
