# Cataloging read-chain map (interim)

## Scope
Interim read-side mapping from the current repository snapshot.

## Primary read chain A — tree read
- Entry point: `App\Controller\CategoryController::tree()`
- Inputs: taxonomy, optional parentId, depth, locale
- Persistence seam: `App\RepositoryInterface\CategoryRepositoryInterface::tree()`

### Status
- Clear chain exists.
- Evidence level: structural only.

## Primary read chain B — by-slug read + breadcrumb
- Entry point: `App\Controller\CategoryController::bySlug()`
- Inputs: taxonomy, slug, locale
- Persistence seam: `CategoryRepositoryInterface::bySlug()`
- Enrichment seam: breadcrumb builder

### Status
- Clear controller -> repository -> breadcrumb assembly chain exists.
- Evidence level: structural only.

## Primary read chain C — API tree
- Entry point: `App\Controller\CategoryApiController::tree()`
- Current behavior: static payload placeholder

### Status
- Route exists, read logic not yet connected to truth source.
- Not RC-grade.

## Primary read chain D — GraphQL read
- Query class observed: `App\GraphQl\CategoryQuery`
- Adjacent resolver seams observed:
  - `App\Service\GraphqlResolver`
  - `App\Service\Api\GraphqlResolver`

### Status
- GraphQL seam exists.
- Namespace and runtime authority need further compression and clarification.

## Auxiliary read chains
- Tenant read seam: `App\Service\TenantFilter`
- Search/facet seam: `App\Service\FacetSearchAdvanced`
- Storefront adapter: `App\Service\StorefrontAdapter`
- Projection table query hints found in SQL and service classes.

## High-risk gaps
1. API tree endpoint is placeholder-level.
2. GraphQL has multiple adjacent implementations and naming drift.
3. Projection-backed read truth is not strongly proven by tests.
