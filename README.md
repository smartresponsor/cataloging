# Smart Responsor / Catalog — Release Candidate Candidate

This repository is the current RC-candidate line of the Catalog component.

## Current status

- Canon-clean runtime line after waves W1–W6
- Wrapper `src/[Layer]/Category/**` debt removed from live code
- `class_alias` compatibility layer removed
- duplicate-owner tail collapsed
- local proof contour prepared for:
  - `composer validate`
  - `composer test`
  - `composer smoke:container`
  - `composer smoke:doctrine`
  - `composer smoke:fixture-load`
  - `composer smoke:graphql`
  - `composer report:runtime-proof`
  - `composer report:owner-overlap`
  - `composer report:route-inventory`
  - `composer report:class-alias`

## Source-of-truth entry points

Read these first:

- `docs/catalog-rc-declaration.md`
- `docs/catalog-rc-runbook.md`
- `docs/catalog-api-doc-strategy.md`
- `docs/catalog-doc-entry.md`

## What this RC line includes

- category tree query/read flow
- category create/move/publish flow
- collection/rule support
- import/export support
- SEO/canonical/redirect/sitemap support
- projection/workflow support
- webhook/integration support
- GraphQL and HTTP entry points
- tenant/security/quota policy

## What this RC line does not claim yet

- GA release
- final release changelog
- final public SDK policy
- final stable API compatibility promise
- full production deployment certification across all environments

## API documentation

- Swagger UI: `/api/doc`
- ReDoc: `/api/redoc`
- OpenAPI YAML: `/api/doc/openapi.yaml`

## Quick start

```bash
composer install
composer validate
composer test
composer smoke:container
composer smoke:doctrine
composer smoke:fixture-load
composer smoke:graphql
```

## Documentation note

This repository still contains historical build, migration and ops notes under `docs/` and `report/`.
For RC-candidate usage, prefer the four source-of-truth documents listed above.

## Naming canon

- `docs/canon/catalog-naming-canon.md`
