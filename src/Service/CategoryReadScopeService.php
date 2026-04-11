<?php

declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryReadScopeServiceInterface;
use App\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use App\ValueObject\CategoryProjectionCriteria;
use App\ValueObject\CategoryReadScopeRequest;
use Symfony\Bundle\SecurityBundle\Security;
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
    public function applyTenantScope(CategoryReadScopeRequest $request): CategoryProjectionCriteria
    {
        $criteria = $request->criteria();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $criteria;
        }

        $context = $this->externalIdentityContextResolver->resolveFromRequest($request->request());
        if (null === $context || null === $context->tenant) {
            return null === $criteria->published() ? $criteria->withPublished(true) : $criteria;
        }

        $requestedTenant = $criteria->tenant();
        if (null !== $requestedTenant && $requestedTenant !== $context->tenant) {
            throw new AccessDeniedHttpException('Cross-tenant category read is not allowed for the current actor.');
        }

        return $criteria->withTenant($context->tenant);
    }
}
