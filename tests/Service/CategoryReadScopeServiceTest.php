<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service;

use App\Cataloging\Security\ExternalIdentityContext;
use App\Cataloging\Service\CatalogCategoryReadScopeService;
use App\Cataloging\ServiceInterface\Security\SecurityExternalIdentityContextResolverInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use App\Cataloging\ValueObject\CategoryReadScopeRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class CategoryReadScopeServiceTest extends TestCase
{
    public function testAnonymousReadDefaultsToPublished(): void
    {
        $service = new CatalogCategoryReadScopeService(
            new class implements SecurityExternalIdentityContextResolverInterface {
                public function resolveFromCurrentRequest(): ?ExternalIdentityContext
                {
                    return null;
                }

                public function resolveFromRequest(Request $request): ?ExternalIdentityContext
                {
                    return null;
                }
            },
            $this->createConfiguredMock(Security::class, ['isGranted' => false]),
        );

        $criteria = $service->applyTenantScope(new CategoryReadScopeRequest(new Request(), CategoryProjectionCriteria::fromArray([])));

        self::assertTrue($criteria->published());
    }

    public function testCrossTenantReadIsRejectedForScopedActor(): void
    {
        $service = new CatalogCategoryReadScopeService(
            new class implements SecurityExternalIdentityContextResolverInterface {
                public function resolveFromCurrentRequest(): ?ExternalIdentityContext
                {
                    return null;
                }

                public function resolveFromRequest(Request $request): ?ExternalIdentityContext
                {
                    if ($request->query->has('_return_null')) {
                        return null;
                    }

                    return new ExternalIdentityContext('actor-1', 'tenant-a', ['ROLE_USER'], []);
                }
            },
            $this->createConfiguredMock(Security::class, ['isGranted' => false]),
        );

        $this->expectException(AccessDeniedHttpException::class);
        $service->applyTenantScope(new CategoryReadScopeRequest(
            new Request(query: ['tenant' => 'tenant-b']),
            CategoryProjectionCriteria::fromArray(['tenant' => 'tenant-b']),
        ));
    }
}
