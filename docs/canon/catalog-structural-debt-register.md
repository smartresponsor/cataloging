# Cataloging structural debt register

This register tracks RC-facing structural debt only inside Cataloging responsibility.

## Closed or actively constrained

- Wildcard controller route import is not a Cataloging RC route-export pattern.
- Generic CRUD controller discovery is not owned by Cataloging.
- Deleted storefront controller references are not retained as route-contract truth.
- Placeholder terminology is being normalized to concrete synthetic-seam descriptions where runtime behavior is deterministic.

## Still monitored

- Historical Category/Cataloging vocabulary remains in older documents and compatibility contracts.
- Readiness reports should remain the source for route inventory, boundary readiness, owner overlap, class aliases, API contract readiness, security readiness, and RC readiness.
- Any future storefront or public delivery route must be reintroduced as an explicit Cataloging business route or moved to the owning bounded context.

## Boundary guard

Cataloging may describe category and catalog discovery. It must not absorb Product, Order, Inventory, Billing, UI, Platform, or global identity ownership.

## Required evidence before RC declaration

- Composer metadata validation passes.
- PHP syntax checks pass for tracked PHP sources.
- PHPUnit suite passes or documented skips are intentional.
- Route export exists and does not rely on generic wildcard CRUD/controller scanning.
- README canonical document references resolve to committed files.
- Current changes are reviewed with `git diff` and committed only after proof.
