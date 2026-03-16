# Catalog Wave F — proof/control packaging

Base: `cataloging-212-current-repository-wave-e-docs-api-truth-aligned.zip`

This wave completes the active control/proof surface expected by `composer.json`.

## Added
- `tools/qa/CatalogPhpLint.php`
- `tools/inspection/CatalogClassAliasReport.php`
- `tools/inspection/CatalogRouteInventoryReport.php`
- `tools/inspection/CatalogOwnerOverlapReport.php`
- `tools/inspection/CatalogRuntimeProofReport.php`
- `tools/smoke/category-runtime-smoke.php`
- `tools/smoke/category-fixture-sanity.php`
- `tools/smoke/category-container-boot-smoke.php`
- `tools/smoke/category-doctrine-mapping-smoke.php`
- `tools/smoke/category-fixture-load-smoke.php`
- `tools/smoke/category-graphql-smoke.php`

## Purpose
Bring the repository to a state where the declared proof/control scripts actually exist in the working tree.
