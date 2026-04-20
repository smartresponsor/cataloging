<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service;

use App\Cataloging\Service\CategoryMutationRequestContextResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

final class CategoryMutationRequestContextResolverTest extends TestCase
{
    public function testActorIdUsesAuthenticatedUserIdentifierWhenAvailable(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('owner-1');

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $resolver = new CategoryMutationRequestContextResolver($security);

        self::assertSame('owner-1', $resolver->actorId(new Request()));
    }

    public function testActorIdFallsBackToHeaderAndThenDefault(): void
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $resolver = new CategoryMutationRequestContextResolver($security);

        self::assertSame('header-actor', $resolver->actorId(new Request(server: ['HTTP_X_ACTOR_ID' => 'header-actor'])));
        self::assertSame('category-api', $resolver->actorId(new Request()));
    }

    public function testIdempotencyAndCorrelationKeysAreResolvedFromHeaders(): void
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);
        $resolver = new CategoryMutationRequestContextResolver($security);

        $request = new Request(server: [
            'HTTP_X_IDEMPOTENCY_KEY' => 'idem-1',
            'HTTP_X_CORRELATION_ID' => 'corr-1',
        ]);

        self::assertSame('idem-1', $resolver->idempotencyKey($request));
        self::assertSame('corr-1', $resolver->correlationId($request));
    }
}
