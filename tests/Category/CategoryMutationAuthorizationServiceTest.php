<?php

declare(strict_types=1);

namespace App\Tests\Category;

use App\Service\CategoryMutationAuthorizationService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use PHPUnit\Framework\TestCase;

final class CategoryMutationAuthorizationServiceTest extends TestCase
{
    public function testAdminCanMoveAndPublish(): void
    {
        $security = $this->createMock(Security::class);
        $security
            ->expects(self::exactly(2))
            ->method('isGranted')
            ->willReturnCallback(static fn (string $attribute): bool => 'ROLE_ADMIN' === $attribute);

        $service = new CategoryMutationAuthorizationService($security);
        $service->assertCanMove('cat-1');
        $service->assertCanPublish('cat-1');

        self::assertTrue(true);
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

        $service = new CategoryMutationAuthorizationService($security);
        $service->assertCanMove('cat-1');

        self::assertTrue(true);
    }

    public function testPublishDeniedWhenNeitherAdminNorPublisher(): void
    {
        $security = $this->createMock(Security::class);
        $security
            ->expects(self::exactly(2))
            ->method('isGranted')
            ->willReturn(false);

        $service = new CategoryMutationAuthorizationService($security);

        $this->expectException(AccessDeniedHttpException::class);
        $service->assertCanPublish('cat-1');
    }
}
