# Catalog wave B — top-level Catalog convergence

Scope:
- rename obvious component-surface `tests*` root classes to `Catalog*`
- do not yet rename inner category unit classes
- do not yet touch nested `src/*/tests/**` component subtrees outside the selected root files

Applied renames:
- `src/Repository/CatalogtestsRepository.php` -> `src/Repository/CatalogRepository.php`
- `src/Security/testsVoter.php` -> `src/Security/CatalogVoter.php`
- `src/Observability/testsProjectionMetrics.php` -> `src/Observability/CatalogProjectionMetrics.php`
- `src/Command/testsSeedCommand.php` -> `src/Command/CatalogSeedCommand.php`
- `src/Command/testsSlugSmokeCommand.php` -> `src/Command/CatalogSlugSmokeCommand.php`
- `src/GraphQl/testsStateProvider.php` -> `src/GraphQl/CatalogStateProvider.php`
