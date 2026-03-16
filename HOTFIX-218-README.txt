This hotfix addresses the current PHPUnit blockers after the voter/services hotfix.

Fixed:
- class/file name mismatches in selected tests
- broken class names in CatalogCategoryCacheService and CategoryAuditLogger
- broken GraphQl query namespace/class mismatch in src/GraphQl/CategoryQuery.php

This is a targeted blocker fix, not a full cleanup of all remaining `tests`-corruption across the repository.
