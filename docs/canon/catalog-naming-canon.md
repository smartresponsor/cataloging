# Catalog naming canon

reviewed_at: 2026-03-13
status: active canon

## Ladder

- `Cataloging` = repository/workspace/capability
- `Catalog` = top-level component entity and first token for component-facing files
- `Category` = subordinate unit inside a catalog

## Rules

- Do not use `Cataloging` as the first token for internal component files.
- For docs, specs, reports, ops and config files that describe the component as a whole, use `catalog-...`
- For PHP tool files, use `Catalog...PascalCase.php`
- Reserve `Category...` for subordinate domain concepts inside the catalog
