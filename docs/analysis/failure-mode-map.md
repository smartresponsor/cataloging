# Wave 6 — failure-mode map

## Webhook endpoint unavailable

### Observed seams
- webhook dispatchers/publishers exist
- operational clue files/logging surfaces exist

### Expected operator action
- inspect webhook contract tests and recent telemetry
- verify endpoint config in package/webhook config
- replay or re-dispatch from outbox path if applicable

### Current weakness
- retry semantics are still not strongly proven end-to-end

---

## Projection lag grows

### Observed seams
- lag gauge at `/metrics`
- lag artifact: `report/category-projection-lag.json`
- projection run report: `report/category-projection-runner.json`

### Expected operator action
- run `php bin/console app:category:projection:run --once`
- compare pre/post lag values
- inspect projection control/pause state and outbox backlog assumptions

### Current weakness
- no durable queue-depth proof in this snapshot

---

## Partial import failure

### Observed seams
- import command/importer chain exists
- DLQ and import error reports exist in `report/*`

### Expected operator action
- inspect `report/category-import-errors.json`
- inspect `report/category-import-dlq.json`
- isolate bad payload batch before replay

### Current weakness
- import truth tests remain thinner than mutation/read backbone

---

## Invalid move/publish request

### Observed seams
- request validation classes exist
- tree invariant tests exist
- publish boolean validation tightened in W4

### Expected operator action
- confirm request DTO validation failure
- inspect caller payload and tenant/role assumptions
- re-run golden flow or controller truth tests against the failing shape

### Current weakness
- kernel-level validation proof is still narrower than total API surface

---

## Stale cache after mutation

### Observed seams
- cache invalidation log artifact exists: `report/category-cache-invalidated.log`
- cache header/middleware seams were normalized in W5

### Expected operator action
- inspect invalidation log
- verify read path hits updated repository/projection truth seam
- compare stale response against current tree/bySlug state

### Current weakness
- cache truth is still more seam-level than fully integration-level
