# Wave H2 — competing-code compression

Base: current slice + G2 + voter compatibility fix.

Applied in this wave:
- Api GraphqlResolver collapsed into wrapper over canonical App\Cataloging\Service\GraphqlResolver
- Integration QuotaGuard collapsed into wrapper over App\Cataloging\Service\QuotaGuard
- Integration SseBroadcaster collapsed into wrapper over App\Cataloging\Service\SseBroadcaster
- Security TenantRolePolicy collapsed into wrapper over App\Cataloging\Service\TenantRolePolicy
- Security JwkConverter collapsed into wrapper over App\Cataloging\Service\JwkConverter
- Seo CanonicalPolicy collapsed into wrapper over App\Cataloging\Service\CanonicalPolicy
- Seo RedirectRule collapsed into wrapper over App\Cataloging\Service\RedirectRule
- Seo SlugVersionPolicy collapsed into wrapper over App\Cataloging\Service\SlugVersionPolicy
- Interface duplicates normalized to alias-extensions:
  - App\Cataloging\ServiceInterface\Api\GraphqlResolverInterface
  - App\Cataloging\ServiceInterface\Security\TenantRolePolicyInterface
  - App\Cataloging\ServiceInterface\Integration\WebhookClientInterface
  - App\Cataloging\ServiceInterface\Seo\CanonicalPolicyInterface

Intentionally left for a later semantic wave:
- App\Cataloging\Api\Graphql\CategoryResolver vs App\Cataloging\GraphQl\CategoryResolver
- App\Cataloging\Projection\CategoryProjectionRunner vs App\Cataloging\Runner\CategoryProjectionRunner
- App\Cataloging\Service\WebhookClient vs App\Cataloging\Service\Integration\WebhookClient
- App\Cataloging\Service\OidcJwtVerifier vs App\Cataloging\Service\Security\OidcJwtVerifier
- App\Cataloging\Service\SitemapGenerator vs App\Cataloging\Service\Seo\SitemapGenerator
