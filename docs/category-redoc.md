# Category ReDoc

This RC line also exposes a static ReDoc viewer over the same reviewed OpenAPI file.

## Paths

- UI: `/api/redoc`
- OpenAPI YAML: `/api/redoc/openapi.yaml`
- Static UI entrypoint: `/doc/redoc/`

## Purpose

ReDoc is provided as a second human-readable API viewer next to Swagger UI.

Recommended use:
- Swagger UI for quick interactive review
- ReDoc for calmer reading and document-style navigation

## Source of truth

- Reviewed contract draft: `api/category-openapi.yaml`
- Web-served copy for ReDoc: `public/doc/redoc/category-openapi.yaml`
