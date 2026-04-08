<?php

declare(strict_types=1);

namespace App\Service;

use App\Security\CategoryVoter;
use App\Security\ExternalIdentityContext;
use App\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use App\ServiceInterface\TenantRolePolicyInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
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
        private ManagerRegistry $registry,
        private SecurityExternalIdentityContextResolverInterface $externalIdentityContextResolver,
        private TenantRolePolicyInterface $tenantRolePolicy,
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
        $this->assertGranted(CategoryVoter::PUBLISH, $categoryId, 'Category publish is not allowed for the current actor.');
    }

    private function assertGranted(string $attribute, string $categoryId, string $message): void
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $categoryTenant = $this->categoryTenant($categoryId);
        $externalIdentityContext = $this->resolveExternalIdentityContext();
        if (null !== $externalIdentityContext && null !== $externalIdentityContext->tenant && null !== $categoryTenant && $externalIdentityContext->tenant !== $categoryTenant) {
            throw new AccessDeniedHttpException('Cross-tenant category mutation is not allowed for the current actor.');
        }

        $subject = new \App\Entity\Category();
        $subject->id = trim($categoryId);

        if ($this->security->isGranted($attribute, $subject)) {
            return;
        }

        if (null !== $externalIdentityContext && $this->externalTenantRoleAllows($attribute, $externalIdentityContext->tenant, $externalIdentityContext->categoryRoles, $categoryTenant)) {
            return;
        }

        throw new AccessDeniedHttpException($message);
    }

    private function categoryTenant(string $categoryId): ?string
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection('data');
        $tenant = $connection->fetchOne('SELECT tenant FROM category WHERE id = :id LIMIT 1', ['id' => trim($categoryId)]);

        if (!is_scalar($tenant)) {
            return null;
        }

        $normalized = trim((string) $tenant);

        return '' !== $normalized ? $normalized : 'default';
    }

    private function resolveExternalIdentityContext(): ?ExternalIdentityContext
    {
        try {
            return $this->externalIdentityContextResolver->resolveFromCurrentRequest();
        } catch (\Throwable) {
            throw new AccessDeniedHttpException('External identity context is invalid or could not be resolved.');
        }
    }

    /** @param list<string> $categoryRoles */
    private function externalTenantRoleAllows(string $attribute, ?string $actorTenant, array $categoryRoles, ?string $categoryTenant): bool
    {
        if ([] === $categoryRoles) {
            return false;
        }

        $tenant = $categoryTenant ?? $actorTenant ?? 'default';
        $action = match ($attribute) {
            CategoryVoter::EDIT => 'edit',
            CategoryVoter::PUBLISH => 'publish',
            CategoryVoter::VIEW => 'read',
            CategoryVoter::OWN => 'edit',
            default => null,
        };
        if (null === $action) {
            return false;
        }

        foreach ($categoryRoles as $role) {
            if ($this->tenantRolePolicy->allow(['org' => $tenant, 'tenant' => $tenant, 'role' => $role], $action)) {
                return true;
            }
        }

        return false;
    }
}
