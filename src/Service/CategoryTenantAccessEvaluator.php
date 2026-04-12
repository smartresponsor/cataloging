<?php

declare(strict_types=1);

namespace App\Service;

use App\Security\CategoryVoter;
use App\Security\ExternalIdentityContext;
use App\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use App\ServiceInterface\TenantRolePolicyInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Centralizes tenant-bound authorization helpers shared by category authorization services.
 */
final readonly class CategoryTenantAccessEvaluator
{
    public function __construct(
        private ManagerRegistry $registry,
        private SecurityExternalIdentityContextResolverInterface $externalIdentityContextResolver,
        private TenantRolePolicyInterface $tenantRolePolicy,
    ) {
    }

    /** @throws Exception */
    public function categoryTenant(string $categoryId): ?string
    {
        /** @var Connection $connection */
        $connection = $this->registry->getConnection('data');
        $tenant = $connection->fetchOne(
            'SELECT tenant FROM category WHERE id = :id LIMIT 1',
            ['id' => trim($categoryId)],
        );

        if (!is_scalar($tenant)) {
            return null;
        }

        $normalized = trim((string) $tenant);

        return '' !== $normalized ? $normalized : 'default';
    }

    public function resolveExternalIdentityContext(): ?ExternalIdentityContext
    {
        try {
            return $this->externalIdentityContextResolver->resolveFromCurrentRequest();
        } catch (\Throwable) {
            throw new AccessDeniedHttpException('External identity context is invalid or could not be resolved.');
        }
    }

    /** @param list<string> $categoryRoles */
    public function externalTenantRoleAllows(
        string $attribute,
        ?string $actorTenant,
        array $categoryRoles,
        ?string $categoryTenant,
    ): bool {
        if ([] === $categoryRoles) {
            return false;
        }

        $tenant = $categoryTenant ?? $actorTenant ?? 'default';
        $action = $this->actionForAttribute($attribute);
        if (null === $action) {
            return false;
        }

        return array_any(
            $this->normalizeCategoryRoles($categoryRoles),
            fn (string $role): bool => $this->tenantRolePolicy->allow(
                ['org' => $tenant, 'tenant' => $tenant, 'role' => $role],
                $action,
            ),
        );
    }

    private function actionForAttribute(string $attribute): ?string
    {
        return match ($attribute) {
            CategoryVoter::EDIT, CategoryVoter::OWN => 'edit',
            CategoryVoter::PUBLISH => 'publish',
            CategoryVoter::VIEW => 'read',
            default => null,
        };
    }

    /** @param list<string> $categoryRoles
     * @return list<string>
     */
    private function normalizeCategoryRoles(array $categoryRoles): array
    {
        return array_values(array_filter(
            $categoryRoles,
            static fn (mixed $role): bool => is_string($role) && '' !== trim($role),
        ));
    }
}
