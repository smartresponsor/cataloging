# Cataloging documentation entry

Cataloging owns catalog and category discovery semantics inside the SmartResponsor platform.

This repository does not own Product, Order, Inventory, Billing, UI, Platform, or other bounded contexts. Integration with those contexts must use explicit Composer dependencies, public contracts, events, DTOs, route imports, or host composition.

## Canonical local documents

- `README.md` — repository status and proof contour.
- `AGENTS.md` — local implementation rules and Symfony-oriented canon.
- `docs/catalog-current-status.md` — current RC state and active boundaries.
- `docs/category-rc-readiness.md` — machine-readable readiness procedure.
- `docs/canon/catalog-structural-debt-register.md` — known RC-facing debt ledger.
- `docs/canon/catalog-layer3-naming-contract.md` — local naming and layer contract.
- `docs/component/integration.adoc` — Symfony bundle integration notes.

## RC boundary

Cataloging may expose:

- catalog/category taxonomy and hierarchy operations;
- category projection, read scope, and discovery data;
- catalog-owned import, export, search, workflow, syndication, attachment policy, and outbox/projection infrastructure;
- bundle configuration required to consume Cataloging routes, services, Doctrine mapping, Messenger routing, API Platform resources, and security rules.

Cataloging must not own:

- product inventory or stock state;
- order lifecycle;
- billing/payment state;
- UI provider/component ownership;
- platform-wide tenant, identity, or security primitives beyond Cataloging-specific policy enforcement.

Generic CRUD route grammar and generic CRUD controllers remain outside Cataloging.
