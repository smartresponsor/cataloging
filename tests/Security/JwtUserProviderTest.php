<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\JwtUser;
use App\Security\JwtUserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\InMemoryUser;

final class JwtUserProviderTest extends TestCase
{
    public function testProviderDefaultsToLeastPrivilegeRole(): void
    {
        $provider = new JwtUserProvider();
        $user = $provider->loadUserByIdentifier('catalog-editor');

        self::assertSame('catalog-editor', $user->getUserIdentifier());
        self::assertSame(['ROLE_USER'], $user->getRoles());
    }

    public function testProviderEscalatesOnlyConfiguredAdminIdentifiers(): void
    {
        $provider = new JwtUserProvider(['ROLE_USER'], ['admin-1']);

        self::assertSame(['ROLE_ADMIN'], $provider->loadUserByIdentifier('admin-1')->getRoles());
        self::assertSame(['ROLE_USER'], $provider->loadUserByIdentifier('editor-1')->getRoles());
    }

    public function testRefreshRejectsUnsupportedUserInstance(): void
    {
        $provider = new JwtUserProvider();

        $this->expectException(\InvalidArgumentException::class);
        $provider->refreshUser(new InMemoryUser('legacy', null, ['ROLE_ADMIN']));
    }

    public function testProviderSupportsOnlyJwtUserClass(): void
    {
        $provider = new JwtUserProvider();

        self::assertTrue($provider->supportsClass(JwtUser::class));
        self::assertFalse($provider->supportsClass(InMemoryUser::class));
    }
}
