# Catalog Wave G1 — domain-wrapper evacuation (minimal scope)

Base: current user slice `Cataloging.zip`

This wave evacuates the non-canonical wrapper:

- `src/Service/CatalogCategory/Domain/**`
- `src/ServiceInterface/CatalogCategory/Domain/**`

to the canonical parent paths:

- `src/Service/CatalogCategory/**`
- `src/ServiceInterface/CatalogCategory/**`

Applied moves: 23
Removed wrapper dirs: 2
