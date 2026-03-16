# Catalog structural debt register

This register tracks the still-visible canon debt in the current slice.

## Active debt groups

### Documentation debt
- many `docs/category-*.md` documents still exist as historical working notes
- only a small subset of docs is already expressed through `catalog-*` entrypoints

### API truth debt
- `api/category-openapi.yaml` still exists for compatibility
- canonical naming should converge on `api/catalog-openapi.yaml`

### Runtime naming debt
- `Catalog` and `Category` naming still coexist in multiple component surfaces
- some names are still transitional and require one more focused cleanup pass

## What is no longer treated as active debt
- root `src/Domain*`, `src/Infra*`, `src/Adapter`, `src/Http` layers
- `src/Service/Category/**` and `src/ServiceInterface/Category/**` wrappers
- old `tests/Category/**`-anchored proof structure
