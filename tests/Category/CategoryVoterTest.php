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
use Symfony\Component\Security\Core\User\InMemoryUser;

final class CategoryVoterTest extends TestCase
{
    public function testAdminCanPublish(): void
    {
        $voter = new CategoryVoter();
        $token = new UsernamePasswordToken(new InMemoryUser('admin', null, ['ROLE_ADMIN']), 'main');

        $res = $voter->vote($token, null, [CategoryVoter::PUBLISH]);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $res);
    }

    public function testEditorCannotPublishWithoutPublisherCapability(): void
    {
        $voter = new CategoryVoter();
        $token = new UsernamePasswordToken(new InMemoryUser('editor', null, ['category.editor']), 'main');

        $res = $voter->vote($token, null, [CategoryVoter::PUBLISH]);

        self::assertSame(VoterInterface::ACCESS_DENIED, $res);
    }
}
