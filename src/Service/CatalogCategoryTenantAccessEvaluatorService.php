<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\ServiceInterface\CatalogTenantRolePolicyServiceInterface;
use App\Cataloging\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use App\Cataloging\ValueObject\Security\ExternalIdentityContext;
use App\Cataloging\Voter\CategoryVoter;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Centralizes tenant-bound authorization helpers shared by category authorization services.
 */
final readonly class CatalogCategoryTenantAccessEvaluatorService
{
    public function __construct(
        private ManagerRegistry $registry,
        private SecurityExternalIdentityContextResolverInterface $externalIdentityContextResolver,
        private CatalogTenantRolePolicyServiceInterface $tenantRolePolicy,
    ) {
    }

    public function categoryTenant(string $categoryId): ?string
    {
        $normalizedId = trim($categoryId);
        $entityManager = $this->categoryEntityManager();
        if ($entityManager instanceof EntityManagerInterface) {
            $entity = $this->findCategoryEntity($entityManager, $normalizedId);
            if ($entity instanceof CatalogCategoryEntity) {
                $tenant = trim($entity->getTenant());

                return '' !== $tenant ? $tenant : 'default';
            }
        }

        try {
            $connection = $this->registry->getConnection('data');
        } catch (\Throwable) {
            return null;
        }

        if (!$connection instanceof Connection) {
            return null;
        }

        $tenant = $connection->fetchOne('SELECT tenant FROM category WHERE id = ? OR slug = ?', [$normalizedId, $normalizedId]);
        if (!is_string($tenant)) {
            return null;
        }

        $normalizedTenant = trim($tenant);

        return '' !== $normalizedTenant ? $normalizedTenant : 'default';
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

    private function categoryEntityManager(): ?EntityManagerInterface
    {
        $manager = $this->registry->getManagerForClass(CatalogCategoryEntity::class);

        return $manager instanceof EntityManagerInterface ? $manager : null;
    }

    private function findCategoryEntity(EntityManagerInterface $entityManager, string $id): ?CatalogCategoryEntity
    {
        $normalizedId = trim($id);
        if ('' === $normalizedId) {
            return null;
        }

        $repository = $entityManager->getRepository(CatalogCategoryEntity::class);
        if (is_numeric($normalizedId)) {
            $entity = $repository->find((int) $normalizedId);
            if ($entity instanceof CatalogCategoryEntity) {
                return $entity;
            }
        }

        $entity = $repository->findOneBy(['slug' => $normalizedId]);
        if ($entity instanceof CatalogCategoryEntity) {
            return $entity;
        }

        $entity = $repository->find($normalizedId);

        return $entity instanceof CatalogCategoryEntity ? $entity : null;
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
        $normalized = [];
        foreach ($categoryRoles as $role) {
            $trimmed = trim($role);
            if ('' !== $trimmed) {
                $normalized[] = $trimmed;
            }
        }

        return $normalized;
    }
}
