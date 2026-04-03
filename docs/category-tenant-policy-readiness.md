# Category tenant policy readiness

This readiness layer verifies that Cataloging treats tenant scope as a local authorization boundary rather than as a local authentication platform.

## Current expectations

- external identity enters through a validated bearer JWT boundary
- external claims are mapped into a local `ExternalIdentityContext`
- category mutation authorization rejects cross-tenant writes
- category search defaults anonymous traffic to published results only
- category search rejects explicit cross-tenant reads for non-admin actors

## Non-goals

- local login/password/session flows
- embedded identity provider behavior
- tenant provisioning platform concerns

## Notes

This layer intentionally assumes that identity issuance remains external. Cataloging only resolves trusted claims and enforces local category-level and tenant-level policy.
