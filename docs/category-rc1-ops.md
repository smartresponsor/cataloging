# Category RC1 Ops

Run smoke after deploy:

```bash
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:category:projection:run --once
curl http://localhost:8080/metrics
```

Verify these artifacts exist after the smoke:

- `report/category-projection-runner.json`
- `report/category-projection-lag.json` (or confirm lag is intentionally zero/not yet emitted)
- `report/category-telemetry.ndjson`
- `report/category-cache-invalidated.log` when cache-invalidation paths were exercised
