<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ValueObject\Security\CategoryAuthorizationSubject;
use App\Cataloging\Voter\CategoryVoter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides the category mutation authorization service application service.
 */
final readonly class CategoryMutationAuthorizationService
{
    /**
     * Initializes the category mutation authorization service service collaborators.
     */
    public function __construct(
        private Security $security,
        private CategoryTenantAccessEvaluator $tenantAccessEvaluator,
    ) {
    }

    /**
     * Handles the assert can move workflow.
     */
    public function assertCanMove(string $categoryId): void
    {
        $this->assertGranted(CategoryVoter::EDIT, $categoryId, 'Category move is not allowed for the current actor.');
    }

    /**
     * Handles the assert can publish workflow.
     */
    public function assertCanPublish(string $categoryId): void
    {
        $this->assertGranted(
            CategoryVoter::PUBLISH,
            $categoryId,
            'Category publish is not allowed for the current actor.',
        );
    }

    private function assertGranted(string $attribute, string $categoryId, string $message): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $categoryTenant = $this->tenantAccessEvaluator->categoryTenant($categoryId);
        $externalIdentityContext = $this->tenantAccessEvaluator->resolveExternalIdentityContext();
        if (
            null !== $externalIdentityContext
            && null !== $externalIdentityContext->tenant
            && null !== $categoryTenant
            && $externalIdentityContext->tenant !== $categoryTenant
        ) {
            throw new AccessDeniedHttpException('Cross-tenant category mutation is not allowed for the current actor.');
        }

        $subject = new CategoryAuthorizationSubject(trim($categoryId));

        if ($this->security->isGranted($attribute, $subject)) {
            return;
        }

        if (
            null !== $externalIdentityContext
            && $this->tenantAccessEvaluator->externalTenantRoleAllows(
                $attribute,
                $externalIdentityContext->tenant,
                $externalIdentityContext->categoryRoles,
                $categoryTenant,
            )
        ) {
            return;
        }

        throw new AccessDeniedHttpException($message);
    }
}
