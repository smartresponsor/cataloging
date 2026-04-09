# Category Security Readiness

This report gives a narrow RC-oriented security signal for the Catalog/Category component.

It is intentionally not a full threat model. It focuses on the surfaces most likely to create release-grade security drift in this repository:

- least-privilege JWT defaults;
- explicit admin allowlist wiring;
- API firewall presence;
- protection of admin, publish, move, attachment-write, and webhook-test routes;
- policy-based authorization on mutation routes, not only coarse route-level RBAC;
- durable access-assignment storage backing category-level authorization decisions;
- baseline OIDC/JWKS readiness artifacts.

## Report

Run:

```bash
composer report:security-readiness
```

The machine-readable output is written to:

- `report/inspection/catalog-security-readiness-report.json`

## Interpretation

- `pass` means the checked surface is present and hardened to the repository's current RC baseline.
- `warn` means the surface is visible but not strong enough to be treated as fully hardened.
- `fail` means the surface is missing or obviously under-protected.

## Current scope

This report is intentionally conservative. It does not try to prove full authentication correctness or end-to-end token validation. Those remain a higher-order runtime/infrastructure concern.
