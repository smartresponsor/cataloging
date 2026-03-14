<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Security;

use App\Security\CategoryVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CatalogCategoryVoterTest extends TestCase
{
    public function testOwnerCanUseEditorCapability(): void
    {
        $user = new class implements UserInterface {
            public function getRoles(): array
            {
                return ['ROLE_CATEGORY_OWNER'];
            }

            public function eraseCredentials(): void
            {
            }

            public function getUserIdentifier(): string
            {
                return 'owner';
            }
        };

        $voter = new CategoryVoter();
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        $result = $voter->vote($token, null, [CategoryVoter::EDITOR]);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }
}
