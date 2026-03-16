# Catalog current status

This document reflects the current repository slice only.

## Repository status

The repository is a **working RC line**, not a final canon-clean release.

### Structural progress already present
- root legacy layers have been evacuated from `src/Domain*`, `src/Infra*`, `src/Adapter`, `src/Http`
- service/service-interface `Category` wrappers have been flattened
- test layer is no longer anchored on `tests/Category/**` only
- runtime baseline is aligned to PHP 8.4

### Structural debt still visible
- many historical `docs/category-*.md` files remain
- both `Category` and `Catalog` vocabulary still coexist in runtime surfaces
- the API contract still keeps a legacy compatibility file under `api/category-openapi.yaml`

## Practical interpretation

You should treat this repository as:
- stable enough for continued canon migration work
- not yet final for a "canon-clean" statement
- requiring one more docs/API truth reset and one more component-surface cleanup pass
