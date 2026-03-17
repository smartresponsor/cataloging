# Category observability

## Live endpoints and artifacts

- Prometheus-style scrape endpoint: `/metrics`
- HTTP request event buffer: `sys_get_temp_dir()/sr_metrics/category_http.jsonl`
- Projection lag source: `report/category-projection-lag.json`
- Projection run report: `report/category-projection-runner.json`
- Telemetry review surface: `report/category-telemetry.ndjson`

## Practical checks

1. run projection once:
   `php bin/console app:category:projection:run --once`
2. scrape metrics:
   `curl http://localhost:8080/metrics`
3. inspect generated operational reports:
   - `report/category-projection-runner.json`
   - `report/category-projection-lag.json`
   - `report/category-telemetry.ndjson`

## Current realism note

Metrics are now exposed in Prometheus text format from current local artifacts. This is sufficient for RC-stage operational probing, but not yet a substitute for durable metric storage or distributed telemetry.
