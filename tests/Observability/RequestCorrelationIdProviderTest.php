<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Observability;

use App\Cataloging\Observability\RequestCorrelationIdProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestCorrelationIdProviderTest extends TestCase
{
    public function testCurrentReturnsNullWithoutActiveRequest(): void
    {
        $provider = new RequestCorrelationIdProvider(new RequestStack());

        self::assertNull($provider->current());
    }

    public function testCurrentReturnsCorrelationIdFromRequestAttribute(): void
    {
        $request = new Request();
        $request->attributes->set(RequestCorrelationIdProvider::ATTRIBUTE, 'corr-123');

        $stack = new RequestStack();
        $stack->push($request);

        $provider = new RequestCorrelationIdProvider($stack);

        self::assertSame('corr-123', $provider->current());
    }
}
