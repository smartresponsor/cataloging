# Category migration readiness

## Current machine-readable signal

Run:

```bash
composer report:migration-readiness
```

Artifact:

- `report/inspection/catalog-migration-readiness-report.json`

## Current findings on the working RC baseline

The current migration layer is **boot-compatible** and now **zero-downtime ready for the forward path**.

The report now distinguishes between two different concerns:

- **forward migration readiness**: duplicate creates, non-canonical version tokens, destructive SQL in `up()`
- **rollback destructiveness**: destructive SQL in `down()` that is still useful diagnostic context, but is not by itself a forward-path RC blocker

## Why this matters

This keeps the migration signal architecture-first.

The repository should warn when the live rollout path is unsafe or contradictory. It should not stay red just because rollback SQL contains `DROP TABLE` or `DROP COLUMN`, which can be normal for `down()` logic.

## Current readiness semantics

- `overallStatus = pass` means the migration lineage is coherent enough for RC conditioning
- `zeroDowntimeReady = true` means forward-path migration discipline is acceptable
- `rollbackDestructiveOnly = true` means rollback SQL is still destructive, but that fact is exposed as diagnostics instead of incorrectly degrading the RC gate
