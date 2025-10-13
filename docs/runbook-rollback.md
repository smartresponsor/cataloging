# Runbook: Rollback RC2.2
1. Disable write access to importer endpoints.
2. Revert deployment to RC2.1 tag.
3. Restore DB snapshot if schema changed.
4. Reenable reads and monitor error-rate.
