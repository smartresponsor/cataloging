# Category Completeness Backend

K11 step06 introduces a backend completeness/readiness layer for category quality evaluation.

## Scope

The layer evaluates category payloads across:

- slug readiness
- SEO title and description readiness
- body/content readiness
- locale coverage readiness
- media readiness
- alias readiness
- banner readiness
- HTML block readiness

## Output

The completeness service produces:

- normalized boolean checks
- completeness score
- missing required checks
- warnings
- publication-oriented checks for the publication gate service

## Design notes

This is intentionally a backend-only capability pack.

It does not introduce a generic workflow engine, separate domain root, or any Port/Adaptor skeleton. It stays inside the canonical Symfony-oriented `src/` layer structure and can feed existing workflow/publication services without rewriting them.
