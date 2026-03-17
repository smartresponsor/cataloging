# Wave 6 — observability verification

## Verified code paths

### Metrics endpoint
- Runtime endpoint: `App\Observability\MetricsController`
- Path: `/metrics`
- Source artifacts:
  - `sys_get_temp_dir()/sr_metrics/category_http.jsonl`
  - `report/category-projection-lag.json`

### Metrics production seam
- HTTP metric writer: `App\Observability\MetricsSubscriber`
- Projection lag holder/reset seam:
  - `App\Observability\CatalogProjectionMetrics`
  - `App\Projection\CategoryProjectionRunner`

### Operational reports used as observability clues
- `report/category-telemetry.ndjson`
- `report/category-cache-invalidated.log`
- `report/category-dlq-requeue.log`
- `report/category-projection-runner.json`

## W6 conclusion

Observability is no longer only declarative documentation. The repository now has a coherent RC-stage path from runtime events to scrapeable `/metrics` output plus inspectable report files.

## Remaining pressure

- no durable metrics backend yet
- projection lag persistence is still report-driven rather than storage-backed
- dashboards/alerts are present as ops artifacts, but not proven against a booted runtime in this snapshot-only analysis
