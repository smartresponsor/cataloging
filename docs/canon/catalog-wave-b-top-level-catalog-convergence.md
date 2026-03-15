# Catalog wave B — top-level Catalog convergence

Scope:
- rename obvious component-surface `Category*` root classes to `Catalog*`
- do not yet rename inner category unit classes
- do not yet touch nested `src/*/Category/**` component subtrees outside the selected root files

Applied renames:
- `src/Repository/CatalogCategoryRepository.php` -> `src/Repository/CatalogRepository.php`
- `src/Security/CategoryVoter.php` -> `src/Security/CatalogVoter.php`
- `src/Observability/CategoryProjectionMetrics.php` -> `src/Observability/CatalogProjectionMetrics.php`
- `src/Command/CategorySeedCommand.php` -> `src/Command/CatalogSeedCommand.php`
- `src/Command/CategorySlugSmokeCommand.php` -> `src/Command/CatalogSlugSmokeCommand.php`
- `src/GraphQl/CategoryStateProvider.php` -> `src/GraphQl/CatalogStateProvider.php`
