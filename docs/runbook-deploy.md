# Runbook: Deploy RC2 Stabilization
1. Backup DB (logical) and verify restore.
2. Enable maintenance for write paths if needed.
3. Apply migrations (if any) and warm up projections.
4. Run smoke (PowerShell or Bash).
5. Enable canary (read) and monitor SLO alerts.
