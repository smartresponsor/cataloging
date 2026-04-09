# Category read surface readiness

Promote category read surfaces from hardcoded demo payloads to projection-backed read services.

## Scope
- category tree API
- category storefront API
- admin list/tree views
- admin category list API
- merchant category list view

## Expectations
- read surfaces use `CategoryProjectionReadService`
- tenant/publication scope is applied through `CategoryReadScopeService` where needed
- hardcoded `Root` / `Electronics` demo arrays are removed from active read controllers
- canonical and legacy OpenAPI files document read routes that are externally visible
