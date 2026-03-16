# Catalog wave E — docs/API truth alignment

Base: `cataloging-208-current-repository-wave-d-tests-tools-aligned.zip`

This wave does not attempt a risky mass rename of all historical documentation.
Instead, it aligns the repository's active truth surface:

- adds `api/catalog-openapi.yaml` as the canonical working contract
- keeps `api/category-openapi.yaml` as legacy compatibility
- rewrites `README.md` to stop overstating repository cleanliness
- adds truthful entrypoint/status/debt documents for the current slice

## Purpose

The goal of this wave is honesty and navigation:
- people should know what is already clean
- people should know what is still transitional
- active documentation should not promise a final canon state too early
