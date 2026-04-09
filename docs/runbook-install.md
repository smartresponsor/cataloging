# Runbook: Install RC2.2
1. Backup database (logical dump). Verify restore.
2. Deploy importer and security packages.
3. Configure JWKS URL and audience/issuer.
4. Run smoke scripts (importer, sdk, security).
5. Enable canary for importer endpoints (10%). Monitor SLO alerts.
