# Wave 6 — operational readiness

## What changed

- added an actual console entry point for projection smoke/runbook flows:
  - `app:category:projection:run`
- aligned `/metrics` with Prometheus text output expected by ops/canary docs
- updated observability and RC ops docs to concrete, existing artifacts
- added operational analysis docs:
  - observability verification
  - runbook reality audit
  - failure-mode map

## Why this matters

W6 does not expand product behavior. It narrows the distance between code, operational docs, and what an operator can actually probe after deploy.

## Interim verdict

The component is still not a full production RC, but the ops/readiness story is materially more honest and executable than in W5.
