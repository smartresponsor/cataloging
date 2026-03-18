# Wave J2 — error-handling hardening

Base: cumulative active snapshot after I2.

## Scope
Focused only on narrow error-handling remediation targets from the audit baseline:
- `src/Importer/CategoryNdjsonImporter.php`
- `src/Service/BatchImportRunner.php`
- `src/Service/CategoryBulk.php`
- `src/Service/CategoryMoveService.php`
- `src/Service/ImportPipeline.php`
- `src/Service/TreeOperationConcurrency.php`

## What changed
- replaced blanket `catch (\Throwable)` blocks with narrower exception sets where practical
- introduced deterministic validation helpers before service calls
- preserved public method signatures where downstream usage could depend on them
- added `processResult()` to `ImportPipeline` so callers can move to explicit outcomes without breaking existing boolean callers
- upgraded database move/concurrency branches to wrap only PDO/runtime failures rather than all throwables

## Intentionally deferred
The following remain for later semantic waves because they require wider contract changes:
- richer domain-specific exception taxonomy
- structured logger injection across import/bulk/move paths
- full replacement of array result contracts with typed DTOs
