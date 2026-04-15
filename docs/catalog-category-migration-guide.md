# CatalogCategory Migration Guide

## Goal
Move from legacy `Category*` naming to canonical `CatalogCategory*` naming without ambiguity in API/service contracts.

## Canonical replacements

| Legacy | Canonical |
|---|---|
| `CategoryApiController` | `CatalogCategoryApiController` |
| `CategoryMutationService` | `CatalogCategoryMutationService` |
| `CategoryMutationServiceInterface` | `CatalogCategoryMutationServiceInterface` |
| `CategoryMutationAuthorizationService` | `CatalogCategoryMutationAuthorizationService` |
| `CategoryMutationMoveRequest` | `CatalogCategoryMutationMoveRequest` |
| `CategoryMutationPublishRequest` | `CatalogCategoryMutationPublishRequest` |
| `MoveCategoryRequest` | `CatalogCategoryMoveRequest` |
| `PublishCategoryRequest` | `CatalogCategoryPublishRequest` |
| move/publish array-shape result payloads | `CatalogCategoryMoveMutationResult` / `CatalogCategoryPublishMutationResult` |

## Routes
Canonical route names are now:
- `api_catalog_category_tree`
- `api_catalog_category_move`
- `api_catalog_category_publish`

## Error handling model
Catalog category routes should rely on `CatalogCategoryApiExceptionSubscriber` for JSON error mapping (400/403/404/409/500) instead of local controller try/catch branching.

## Request parsing model
Mutation request DTO parsing should use `App\Request\Support\RequestValueNormalizer`:
- trimmed string defaults,
- nullable/strict bool parsing,
- explicit enum validation (e.g. `CatalogCategoryMutationPolicy`).

## Mutation result model
`CatalogCategoryMutationServiceInterface` now returns typed mutation result DTOs:
- `CatalogCategoryMoveMutationResult`
- `CatalogCategoryPublishMutationResult`

Controller/API layers should serialize these DTOs at the edge (currently through `toArray()`) to keep the existing JSON response contract stable while avoiding raw array-shape contracts inside the service layer.

## Anti-corruption boundary
Any new integration code should consume canonical `CatalogCategory*` types only.
If legacy external contracts still send legacy field conventions, adapt them in a thin adapter layer before entering application services.

## Rollout checklist
- [ ] Replace remaining `Category*` service/controller names outside mutation surface.
- [ ] Update OpenAPI/GraphQL docs to canonical `catalog category` language.
- [ ] Remove temporary backward-compat adapters if any are introduced later.
