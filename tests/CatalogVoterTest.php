<?php

declare(strict_types=1);

namespace App\Tests;

use App\Security\CatalogVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CatalogVoterTest extends TestCase
{
    public function testEditorCanUseEditorCapability(): void
    {
        $user = new class implements UserInterface {
            public function getRoles(): array
            {
                return ['ROLE_CATEGORY_EDITOR'];
            }

            public function eraseCredentials(): void
            {
            }

            public function getUserIdentifier(): string
            {
                return 'editor';
            }
        };

        $voter = new CatalogVoter();
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

        $result = $voter->vote($token, null, [CatalogVoter::EDITOR]);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }
}
