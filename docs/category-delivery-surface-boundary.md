# Category delivery surface boundary

Cataloging intentionally serves multiple delivery surfaces:
- admin;
- storefront;
- merchant;
- public/store APIs;
- search;
- GraphQL read adapters.

This is acceptable because the domain boundary is **catalog**, not because each surface gets its own local domain.

## Boundary rule

delivery surfaces must remain adapters over shared application services.
They should not carry their own independent business workflows, persistence models, or local domain forks.

## Expected shape

- write surfaces delegate to mutation services and policy checks;
- read surfaces delegate to projection/search/read scope services;
- tenant/resource policy stays shared;
- surface-specific filtering stays adapter-thin.
