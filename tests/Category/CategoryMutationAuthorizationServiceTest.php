<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Service\CatalogCategoryMutationAuthorizationService;
use App\Cataloging\Service\CatalogCategoryTenantAccessEvaluatorService;
use App\Cataloging\ServiceInterface\CatalogTenantRolePolicyServiceInterface;
use App\Cataloging\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use App\Cataloging\ValueObject\Security\ExternalIdentityContext;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class CatalogCategoryMutationAuthorizationServiceTest extends TestCase
{
    public function testAdminCanMoveAndPublish(): void
    {
        $security = $this->createMock(Security::class);
        $security
            ->expects(self::exactly(2))
            ->method('isGranted')
            ->willReturnCallback(static fn (string $attribute): bool => 'ROLE_ADMIN' === $attribute);

        $service = $this->service($security, 'tenant-a', ['publisher']);
        $service->assertCanMove('cat-1');
        $service->assertCanPublish('cat-1');

        self::addToAssertionCount(1);
    }

    public function testEditorGrantAllowsMove(): void
    {
        $security = $this->createMock(Security::class);
        $security
            ->expects(self::exactly(2))
            ->method('isGranted')
            ->willReturnCallback(static function (string $attribute): bool {
                return 'category.edit' === $attribute;
            });

        $service = $this->service($security, 'tenant-a', ['editor']);
        $service->assertCanMove('cat-1');

        self::addToAssertionCount(1);
    }

    public function testPublishDeniedWhenNeitherAdminNorPublisher(): void
    {
        $security = $this->createMock(Security::class);
        $security
            ->expects(self::exactly(2))
            ->method('isGranted')
            ->willReturn(false);

        $service = $this->service($security, 'tenant-a', []);

        $this->expectException(AccessDeniedHttpException::class);
        $service->assertCanPublish('cat-1');
    }

    /** @param list<string> $roles */
    private function service(Security $security, string $tenant, array $roles): CatalogCategoryMutationAuthorizationService
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchOne')->willReturn('tenant-a');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getConnection')->with('data')->willReturn($connection);

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
                return in_array($action, ['read', 'edit', 'publish'], true)
                    && in_array($ctx['role'], ['publisher', 'editor', 'owner'], true);
            }
        };

        return new CatalogCategoryMutationAuthorizationService(
            $security,
            new CatalogCategoryTenantAccessEvaluatorService($registry, $resolver, $tenantRolePolicy),
        );
    }
}
