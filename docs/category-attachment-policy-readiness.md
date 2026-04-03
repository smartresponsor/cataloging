# Category attachment policy readiness

This report tracks whether category attachment binding behaves as a tenant-scoped domain boundary rather than a broad admin-only shortcut.

## Expectations
- attachment list is not available without scoped category context for non-admin actors;
- attachment add/delete flows are authorized through category-aware policy checks;
- cross-tenant attachment operations are denied;
- external identity context is required for non-admin attachment operations;
- controller logic delegates attachment policy to a dedicated authorization service.
