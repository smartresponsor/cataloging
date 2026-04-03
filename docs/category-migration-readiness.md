# Category migration readiness

## Current machine-readable signal

Run:

```bash
composer report:migration-readiness
```

Artifact:

- `report/inspection/catalog-migration-readiness-report.json`

## Current findings on the working RC baseline

The current migration layer is **boot-compatible** but **not yet zero-downtime clean**.

The report currently flags:

- duplicate category table creation in two migrations
- one non-canonical migration version token
- destructive rollback statements in `down()` methods

## Why this matters

This is not a runtime bootstrap blocker, but it is a release-discipline risk.

The main concern is not that the application cannot boot. The concern is that repeated schema creation for the same table and non-canonical migration naming make migration ordering and rollback reasoning weaker than the rest of the repository baseline.

## Recommended next wave

1. Decide which `category` table creation migration is canonical.
2. Retire or archive the duplicate migration instead of leaving both live.
3. Normalize migration version naming to one canonical format.
4. Move future migration work to explicit expand-contract discipline for zero-downtime readiness.
