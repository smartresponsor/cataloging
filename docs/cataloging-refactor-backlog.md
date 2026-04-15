# Cataloging Refactor Backlog (canonical naming + decomposition)

## Naming canon (Catalog-first)
- [x] Introduce canonical request DTO names with `CatalogCategory*` prefix for mutation API inputs.
- [~] Migrate remaining `Category*` application services/controllers to canonical `CatalogCategory*` names.
  - [x] Removed legacy request DTOs (`MoveCategoryRequest`, `PublishCategoryRequest`) and migrated to canonical DTO-only flow.
  - [~] Rename service/controller class names and route names to canonical `CatalogCategory*`.
    - [x] Mutation API controller renamed to `CatalogCategoryApiController` with canonical route names.
    - [x] Mutation service surface renamed to `CatalogCategoryMutation*` (service, auth service, service interface, tests).
    - [x] Mutation value objects renamed to `CatalogCategoryMutation*Request`.
    - [ ] Complete canonical rename across remaining controller/service/read-model surfaces.
- [ ] Update API contract/docs vocabulary to use `catalog category` wording consistently.

## Decomposition and SOLID
- [ ] Split mutation application flow into separate handlers:
  - `CatalogCategoryMoveHandler`
  - `CatalogCategoryPublishHandler`
- [ ] Extract infrastructure ports for audit/outbox/idempotency/category tree persistence.
- [x] Replace array-shape responses with typed result DTOs.
  - [x] `CatalogCategoryMutationServiceInterface` now returns `CatalogCategoryMoveMutationResult` / `CatalogCategoryPublishMutationResult`.
  - [x] `CatalogCategoryMutationService` move/publish and duplicate paths now build typed DTOs.
  - [x] API controller serializes DTOs via `toArray()` to preserve wire format stability.

## Controller thickness
- [x] Move exception-to-HTTP mapping into centralized problem-details responder/listener.
- [x] Move actor/correlation/idempotency extraction into request context resolver.
  - [x] Replaced message-based 404 detection with explicit `CatalogCategoryNotFoundException` mapping.

## DTO and validation hardening
- [~] Add explicit enum/value objects for policy/workflow identifiers.
  - [x] Added `CatalogCategoryMutationPolicy` enum and wired it into move mutation DTO/VO flow.
  - [x] Renamed workflow state VO/interface to canonical `CatalogCategoryWorkflowState*`.
  - [ ] Extend enum/value-object hardening to remaining read surfaces.
- [~] Consolidate bool coercion and validation rules in reusable normalizers.
  - [x] Added `RequestValueNormalizer` and integrated it into mutation request DTO parsing.
  - [ ] Roll out normalizer usage to remaining request DTOs outside mutation endpoints.

## Testing and quality gates
- [~] Replace dummy API tests with behavioral tests (success/failure/idempotency).
  - [x] Replaced dummy API test with controller behavior tests for move/publish/tree flows.
  - [~] Expand behavioral API coverage to idempotency/error branches.
    - [x] Added controller-level duplicate idempotency response coverage for move flow.
    - [x] Added subscriber-level coverage for 400/403/404/409/500 exception mapping branches.
    - [x] Added controller+subscriber integration checks for 404/409 error serialization.
    - [x] Added controller+subscriber integration checks for 403/400/500 error serialization.
- [~] Add coverage threshold gates in CI/phpunit configuration.
  - [x] Added `test:coverage:gate` composer script with Clover threshold enforcement (`70%`).
  - [x] Integrated coverage gate script into `tools/ci/run-tests.sh` pipeline flow.
- [~] Add fixtures focused on edge cases for path rebasing and concurrency/idempotency replay.
  - [x] Added mutation-service edge case test for idempotency key reuse with different payload.
  - [x] Added dedicated fixture sets for deep path rebasing and replay scenarios (`mutation_deep_tree.yaml`, `mutation_idempotency_replay.yaml`).

## Documentation
- [x] Publish migration guide for renamed canonical classes.
- [x] Document anti-corruption strategy for legacy `Category*` contracts.
