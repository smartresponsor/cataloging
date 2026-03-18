# Wave H2 — competing-code compression

Base: current slice + G2 + voter compatibility fix.

Applied in this wave:
- Api GraphqlResolver collapsed into wrapper over canonical App\Service\GraphqlResolver
- Integration QuotaGuard collapsed into wrapper over App\Service\QuotaGuard
- Integration SseBroadcaster collapsed into wrapper over App\Service\SseBroadcaster
- Security TenantRolePolicy collapsed into wrapper over App\Service\TenantRolePolicy
- Security JwkConverter collapsed into wrapper over App\Service\JwkConverter
- Seo CanonicalPolicy collapsed into wrapper over App\Service\CanonicalPolicy
- Seo RedirectRule collapsed into wrapper over App\Service\RedirectRule
- Seo SlugVersionPolicy collapsed into wrapper over App\Service\SlugVersionPolicy
- Interface duplicates normalized to alias-extensions:
  - App\ServiceInterface\Api\GraphqlResolverInterface
  - App\ServiceInterface\Security\TenantRolePolicyInterface
  - App\ServiceInterface\Integration\WebhookClientInterface
  - App\ServiceInterface\Seo\CanonicalPolicyInterface

Intentionally left for a later semantic wave:
- App\Api\Graphql\CategoryResolver vs App\GraphQl\CategoryResolver
- App\Projection\CategoryProjectionRunner vs App\Runner\CategoryProjectionRunner
- App\Service\WebhookClient vs App\Service\Integration\WebhookClient
- App\Service\OidcJwtVerifier vs App\Service\Security\OidcJwtVerifier
- App\Service\SitemapGenerator vs App\Service\Seo\SitemapGenerator
