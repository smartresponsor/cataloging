# Cataloging security-chain map (interim)

## Observed security seams
- Voter: `App\Security\CategoryVoter`
- Policy contracts/services:
  - `App\Service\TenantRolePolicy`
  - `App\Service\CatalogCategory\Acl\AclPolicyService`
  - `App\PolicyInterface\CategoryPolicyInterface`
- Request-level validation:
  - `App\Request\MoveCategoryRequest`
  - `App\Request\PublishCategoryRequest`
- Config surfaces:
  - `config/policy/*`
  - `config/packages/*` auth/rate-limit adjacent files present in repository structure

## Boundary notes
- Publish authorization is visible at voter/policy level.
- API controllers currently validate transport payloads but do not yet prove full auth-to-mutation enforcement end-to-end.
- Merchant/admin/public boundaries are visible in controller namespaces, but chain proof is incomplete.

## Test evidence
- Direct voter test exists.
- No convincing end-to-end authorization test for move/publish observed in current snapshot.

## Verdict
Security perimeter exists conceptually, but proof remains seam-level rather than flow-level.
