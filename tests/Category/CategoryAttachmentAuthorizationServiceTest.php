<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\RepositoryInterface\Catalog\CatalogAttachmentRepositoryInterface;
use App\Cataloging\Service\CatalogCategoryAttachmentAuthorizationService;
use App\Cataloging\Service\CatalogCategoryTenantAccessEvaluatorService;
use App\Cataloging\ServiceInterface\CatalogTenantRolePolicyServiceInterface;
use App\Cataloging\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use App\Cataloging\ValueObject\Security\ExternalIdentityContext;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class CatalogCategoryAttachmentAuthorizationServiceTest extends TestCase
{
    public function testAttachAllowedForMatchingTenantPublisherRole(): void
    {
        $service = $this->service('tenant-a', ['publisher']);
        $service->assertCanAttach('cat-1');

        self::addToAssertionCount(1);
    }

    public function testAttachDeniedAcrossTenantBoundary(): void
    {
        $service = $this->service('tenant-b', ['publisher']);

        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Cross-tenant category attachment operation is not allowed');
        $service->assertCanAttach('cat-1');
    }

    public function testListWithoutCategoryScopeDeniedForNonAdmin(): void
    {
        $service = $this->service('tenant-a', ['reader']);

        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('Listing attachments without category scope is not allowed');
        $service->assertCanList(null);
    }

    /** @param list<string> $roles */
    private function service(string $tenant, array $roles): CatalogCategoryAttachmentAuthorizationService
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(static fn (string $attribute): bool => 'ROLE_ADMIN' === $attribute ? false : false);

        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')->willReturn('tenant-a');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getConnection')->with('data')->willReturn($connection);

        $repo = $this->createMock(CatalogAttachmentRepositoryInterface::class);
        $repo->method('findOne')->willReturn([
            'attachment_id' => 'att-1',
            'category_id' => 'cat-1',
            'type' => 'banner',
            'provider' => 'media',
            'external_attachment_id' => 'asset-1',
            'reference_uri' => null,
            'path' => null,
            'created_at' => '2026-04-03T00:00:00+00:00',
        ]);

        $resolver = new class($tenant, $roles) implements SecurityExternalIdentityContextResolverInterface {
            /** @param list<string> $roles */
            public function __construct(private readonly string $tenant, private readonly array $roles)
            {
            }

            public function resolveFromCurrentRequest(): ExternalIdentityContext
            {
                return new ExternalIdentityContext('actor-1', $this->tenant, ['ROLE_USER'], $this->roles);
            }

            public function resolveFromRequest(\Symfony\Component\HttpFoundation\Request $request): ExternalIdentityContext
            {
                return $this->resolveFromCurrentRequest();
            }
        };

        $tenantRolePolicy = new class implements CatalogTenantRolePolicyServiceInterface {
            public function allow(array $ctx, string $action): bool
            {
                return in_array($action, ['read', 'edit', 'publish'], true) && in_array($ctx['role'], ['publisher', 'editor', 'owner', 'reader'], true);
            }
        };

        return new CatalogCategoryAttachmentAuthorizationService(
            $security,
            $repo,
            new CatalogCategoryTenantAccessEvaluatorService($registry, $resolver, $tenantRolePolicy),
        );
    }
}
