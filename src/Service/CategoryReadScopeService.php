<?php

declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryReadScopeServiceInterface;
use App\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides the category read scope service application service.
 */
final readonly class CategoryReadScopeService implements CategoryReadScopeServiceInterface
{
    /**
     * Initializes the category read scope service service collaborators.
     */
    public function __construct(
        private SecurityExternalIdentityContextResolverInterface $externalIdentityContextResolver,
        private Security $security,
    ) {
    }

    /**
     * Applies the tenant scope workflow to the provided input.
     */
    public function applyTenantScope(Request $request, array $criteria): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $criteria;
        }

        $context = $this->externalIdentityContextResolver->resolveFromRequest($request);
        if (null === $context || null === $context->tenant) {
            $criteria['published'] ??= true;

            return $criteria;
        }

        $requestedTenant = is_scalar($criteria['tenant'] ?? null) ? trim((string) $criteria['tenant']) : '';
        if ('' !== $requestedTenant && $requestedTenant !== $context->tenant) {
            throw new AccessDeniedHttpException('Cross-tenant category read is not allowed for the current actor.');
        }

        $criteria['tenant'] = $context->tenant;

        return $criteria;
    }
}
