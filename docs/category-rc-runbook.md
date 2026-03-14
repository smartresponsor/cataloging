# Category RC runbook

## Recommended proof order

```bash
composer install
composer validate
composer test
composer smoke:container
composer smoke:doctrine
composer smoke:fixture-load
composer smoke:graphql
composer report:runtime-proof
composer report:owner-overlap
composer report:route-inventory
composer report:class-alias
```

## Expected healthy results

- `composer validate` returns valid
- `composer test` returns green
- smoke commands return healthy JSON with no missing critical files
- `report:class-alias` returns zero aliases
- `report:owner-overlap` returns zero duplicate owner groups

## RC packaging checklist

- keep `README.md` current
- keep `docs/category-rc-declaration.md` current
- keep `api/category-openapi.yaml` current
- keep `composer.json` and `composer.lock` in sync
- keep `MANIFEST.txt` regenerated after structural waves
- keep local proof outputs archived in `report/` when doing final RC validation

## Human-readable API review

- open `/api/doc` in the browser to inspect the current contract through Swagger UI
- use `/api/doc/openapi.yaml` to fetch the raw reviewed specification

- open `/api/redoc` in the browser for the same contract rendered through ReDoc
