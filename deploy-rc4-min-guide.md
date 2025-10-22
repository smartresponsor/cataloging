# Category RC4 — Minimal Deploy Guide (03_0–03_13)
- Migrate DB (PG → MySQL) by phase order, sync projection, warm cache, rebuild search (ICU+synonyms), generate sitemap/hreflang, enable webhooks+quota, expose metrics+status.
- Canary 48–120 h; SLO gates: read p95 ≤ 250 ms, write p95 ≤ 700 ms, error-rate ≤ 0.5%, projection lag ≤ 5 s.
- Rollback via MigrationGuard/RollbackPlan.
