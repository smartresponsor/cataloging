# Catalog structural debt register

reviewed_at: 2026-03-14  
scope: current uploaded slice only

## Highest-severity debt

### Noncanonical wrapper directories
These `Category` wrapper folders exist at depth <= 3 and violate the current canon:

- `src/Adapter/Category` — files: 1
- `src/Audit/Category` — files: 1
- `src/Cache/Category` — files: 1
- `src/CacheInterface/Category` — files: 1
- `src/Command/Category` — files: 4
- `src/Controller/Category` — files: 27
- `src/ControllerInterface/Category` — files: 1
- `src/DataFixtures/Category` — files: 2
- `src/Domain/Category` — files: 21
- `src/DomainInterface/Category` — files: 3
- `src/Entity/Category` — files: 11
- `src/EntityInterface/Category` — files: 4
- `src/Event/Category` — files: 5
- `src/EventInterface/Category` — files: 5
- `src/EventSubscriber/Category` — files: 1
- `src/Exception/Category` — files: 4
- `src/Exporter/Category` — files: 1
- `src/ExporterInterface/Category` — files: 1
- `src/GraphQl/Category` — files: 3
- `src/Http/Category` — files: 2
- `src/Idempotency/Category` — files: 1
- `src/IdempotencyInterface/Category` — files: 1
- `src/Importer/Category` — files: 1
- `src/ImporterInterface/Category` — files: 1
- `src/Infra/Category` — files: 6
- `src/InfraInterface/Category` — files: 2
- `src/Infrastructure/Category` — files: 1
- `src/Logging/Category` — files: 1
- `src/Observability/Category` — files: 1
- `src/Outbox/Category` — files: 1
- `src/OutboxInterface/Category` — files: 1
- `src/Policy/Category` — files: 2
- `src/PolicyInterface/Category` — files: 2
- `src/Projection/Category` — files: 2
- `src/ProjectionInterface/Category` — files: 1
- `src/Repository/Category` — files: 1
- `src/RepositoryInterface/Category` — files: 1
- `src/Request/Category` — files: 2
- `src/Runner/Category` — files: 1
- `src/RunnerInterface/Category` — files: 1
- `src/Security/Category` — files: 6
- `src/ValueObject/Category` — files: 2
- `src/ValueObjectInterface/Category` — files: 1
- `src/Webhook/Category` — files: 1

### Legacy root layers
- `src/Domain/**`
- `src/DomainInterface/**`
- `src/Infra/**`
- `src/InfraInterface/**`
- `src/Adapter/**`
- `src/Http/**`

### Documentation drift
The repository currently contains 23 `docs/category-*.md` files and 0 `docs/catalog-*.md` files.
Current user-facing docs should be moved to `catalog-*`, while `category-*` either becomes historical or is renamed.

### Test structure drift
Current tests top-level layout: Category.
`tests/Category/**` remains noncanonical and should be flattened by direction.

### Tooling drift
Legacy tooling names still exist:
- linter: category_mirror_check.php, category_prefix_check.php
- smoke: category-k6.js, category-smoke.sh
