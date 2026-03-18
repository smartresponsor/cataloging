# catalog-wave-k11-step05 review decision coupling

This wave adds a narrow orchestration layer that couples moderation outcome to workflow and publication readiness.

## Added

- review decision coupling service
- review decision coupling result value object
- review decision coupled event
- focused coupling tests

## Canon position

The wave stays inside canonical Symfony-oriented roots:

- `src/Service`
- `src/ServiceInterface`
- `src/ValueObject`
- `src/ValueObjectInterface`
- `src/Event`
- `src/EventInterface`
- `tests`
- `docs`

No parallel application roots were introduced. No `Domain`, `Port`, `Adaptor`, `Infra`, `Catalog`, or `Cataloging` wrapper trees were added.
