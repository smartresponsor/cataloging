<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Security;

use App\Cataloging\Service\Security\CategoryRole;
use App\Cataloging\Service\Security\ExternalIdentityContextMapper;
use PHPUnit\Framework\TestCase;

final class ExternalIdentityContextMapperTest extends TestCase
{
    public function testMapsSubjectTenantFrameworkRolesAndCategoryRoles(): void
    {
        $mapper = new ExternalIdentityContextMapper();
        $context = $mapper->map([
            'sub' => 'user-1',
            'tenant_id' => 'tenant-a',
            'roles' => ['role_admin', 'ROLE_MERCHANT'],
            'category_roles' => ['editor', 'publisher'],
        ]);

        self::assertSame('user-1', $context->subject);
        self::assertSame('tenant-a', $context->tenant);
        self::assertSame(['ROLE_ADMIN', 'ROLE_MERCHANT', 'ROLE_USER'], $context->frameworkRoles);
        self::assertSame([CategoryRole::EDITOR, CategoryRole::PUBLISHER], $context->categoryRoles);
    }

    public function testMissingSubjectFailsClosed(): void
    {
        $mapper = new ExternalIdentityContextMapper();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('External identity claims must include sub.');

        $mapper->map(['tenant' => 'tenant-a']);
    }
}
