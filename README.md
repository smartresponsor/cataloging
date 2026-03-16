# Smart Responsor / Catalog — RC Working Line

This repository is the current working RC line of the Catalog component.

## Current status

This slice already contains structural migration progress from the canon waves, but it is **not yet a final canon-clean repository**.

What is already in:
- PHP 8.4 runtime/tooling baseline
- service/service-interface wrapper evacuation
- part of top-level `Catalog` convergence
- tests/tools alignment
- syntax stabilization hotfix

What is still transitional:
- many historical `docs/category-*.md` documents
- API contract still carries legacy `category` naming as a compatibility path
- some component surfaces still use mixed `Category` / `Catalog` vocabulary
- final docs/API truth reset is still in progress

## Canon sources of truth

- `docs/catalog-doc-entry.md`
- `docs/catalog-current-status.md`
- `docs/canon/catalog-structural-debt-register.md`

## API contracts

- canonical working contract: `api/catalog-openapi.yaml`
- legacy compatibility contract: `api/category-openapi.yaml`

## Proof contour

Typical local checks:
- `composer validate`
- `composer test`
- `composer report:class-alias`
- `composer report:owner-overlap`
- `composer report:route-inventory`
- `composer report:runtime-proof`
