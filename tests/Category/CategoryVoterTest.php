<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Security\CategoryVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class CategoryVoterTest extends TestCase
{
    public function testAdminCanPublish(): void
    {
        $voter = new CategoryVoter();
        $token = new UsernamePasswordToken('admin', 'pwd', 'main', ['ROLE_ADMIN']);
        $res = $voter->vote($token, null, [CategoryVoter::PUBLISH]);
        self::assertSame(VoterInterface::ACCESS_GRANTED, $res);
    }
}
