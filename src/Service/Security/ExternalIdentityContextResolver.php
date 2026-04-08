<?php

declare(strict_types=1);

namespace App\Service\Security;

use App\Security\ExternalIdentityContext;
use App\ServiceInterface\OidcJwtValidatorInterface;
use App\ServiceInterface\Security\SecurityExternalIdentityContextMapperInterface;
use App\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
/**
 * Provides the external identity context resolver application service.
 */
final readonly class ExternalIdentityContextResolver implements SecurityExternalIdentityContextResolverInterface
{
    private const REQUEST_ATTRIBUTE = '_catalog_external_identity_context';
    /**
     * Initializes the external identity context resolver service collaborators.
     */
    public function __construct(
        private RequestStack $requestStack,
        private OidcJwtValidatorInterface $validator,
        private SecurityExternalIdentityContextMapperInterface $mapper,
    ) {
    }
    /**
     * Resolves the from current request result for the current workflow.
     */
    public function resolveFromCurrentRequest(): ?ExternalIdentityContext
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request instanceof Request ? $this->resolveFromRequest($request) : null;
    }
    /**
     * Resolves the from request result for the current workflow.
     */
    public function resolveFromRequest(Request $request): ?ExternalIdentityContext
    {
        $cached = $request->attributes->get(self::REQUEST_ATTRIBUTE);
        if ($cached instanceof ExternalIdentityContext) {
            return $cached;
        }

        $bearerToken = $this->bearerToken($request);
        if (null === $bearerToken) {
            return null;
        }

        $claims = $this->validator->validate($bearerToken);
        $context = $this->mapper->map($claims);
        $request->attributes->set(self::REQUEST_ATTRIBUTE, $context);

        return $context;
    }

    private function bearerToken(Request $request): ?string
    {
        $authorization = trim((string) $request->headers->get('Authorization', ''));
        if ('' === $authorization || !preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            return null;
        }

        $token = trim((string) $matches[1]);

        return '' !== $token ? $token : null;
    }
}
