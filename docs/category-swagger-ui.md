# Category Swagger UI

This RC line exposes a static Swagger UI viewer on top of the reviewed OpenAPI file.

## Paths

- UI: `/api/doc`
- OpenAPI YAML: `/api/doc/openapi.yaml`
- Static UI entrypoint: `/doc/swagger/`

## Source of truth

- Reviewed contract draft: `api/category-openapi.yaml`
- Web-served copy for the UI: `public/doc/swagger/category-openapi.yaml`

## Why this shape

The repository currently follows an OpenAPI-first RC packaging strategy.
Swagger UI is added as a human-readable viewer over the existing contract, without changing the runtime model of the component.

## Notes

- The OpenAPI file remains the reviewed contract source.
- The public copy exists only so the browser can fetch the specification from the web root.
- `validatorUrl` is disabled in the UI to avoid external validation dependency during local RC review.
