# Wave 6 — runbook reality audit

## Checked runbooks/docs

- `docs/category-observability.md`
- `docs/category-rc1-ops.md`
- `ops/category-canary.yaml`

## Reality adjustments made in W6

1. `docs/category-rc1-ops.md` now points to an actually present command:
   - `app:category:projection:run --once`
2. `docs/category-observability.md` now names the concrete local artifacts that feed `/metrics`.
3. `ops/category-canary.yaml` continues to target `/metrics`, which is now aligned to Prometheus text output.

## Remaining realism gaps

- deployment runbooks still depend on environment-specific wiring not proven in snapshot-only mode
- import/export operational playbooks remain broader than their current test proof
- webhook and projection operational stories are stronger than before, but still short of full live-environment validation
