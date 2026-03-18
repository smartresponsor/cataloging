# Catalog Wave G3 — canary naming cleanup (minimal scope)

Base: current user slice `cataloging-waveK10-step04-cumulative-snapshot.zip`

This wave renames the canary runner path only:

- `tools/canary/run-category-canary.sh`
- `tools/canary/run-catalog-canary.sh`

The internal report filenames were preserved to avoid changing runtime/report semantics in this scope.
