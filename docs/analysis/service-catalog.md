# Cataloging W5 service catalog

## Canonical service roots

Primary canonical roots for runtime behavior:
- `App\Controller\...`
- `App\Security\...`
- `App\Repository\...`
- `App\Infrastructure\...`
- `App\Service\...`

## Compatibility wrappers retained in W5

These remain only as namespace bridges and should not accumulate new logic:
- `App\Service\Api\CatalogCategoryCacheHeader`
- `App\Service\Api\EtagMiddleware`
- `App\Service\Api\GraphqlResolver`

## Service-sprawl findings

High duplication / ambiguity clusters:
- cache headers and middleware (`Service` vs `Service\Api`)
- GraphQL resolver (`Service` vs `Service\Api`)
- tenant role policy (`Service` vs `Service\Security`)
- canonical locale policy legacy residue (`CanonicalPolicyLocale-.php`)

## W5 normalization decisions

1. Keep `App\Service\...` as canonical implementation root.
2. Keep API namespace variants as thin wrappers only.
3. Keep security-scoped tenant role policy separate, but bind it to explicit security interface and shared role constants.
4. Neutralize non-canonical residue file that could introduce duplicate class declarations on accidental include.
