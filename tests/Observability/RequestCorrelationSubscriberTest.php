<?php

declare(strict_types=1);

namespace App\Tests\Observability;

use App\Observability\RequestCorrelationIdProvider;
use App\Observability\RequestCorrelationSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class RequestCorrelationSubscriberTest extends TestCase
{
    public function testRequestUsesIncomingCorrelationIdHeader(): void
    {
        $subscriber = new RequestCorrelationSubscriber();
        $request = new Request();
        $request->headers->set(RequestCorrelationIdProvider::HEADER, ' incoming-id ');

        $subscriber->onRequest($this->createRequestEvent($request));

        self::assertSame('incoming-id', $request->attributes->get(RequestCorrelationIdProvider::ATTRIBUTE));
    }

    public function testRequestGeneratesCorrelationIdWhenHeaderMissing(): void
    {
        $subscriber = new RequestCorrelationSubscriber();
        $request = new Request();

        $subscriber->onRequest($this->createRequestEvent($request));

        $correlationId = $request->attributes->get(RequestCorrelationIdProvider::ATTRIBUTE);

        self::assertIsString($correlationId);
        self::assertSame(32, strlen($correlationId));
    }

    public function testResponseAddsCorrelationIdHeader(): void
    {
        $subscriber = new RequestCorrelationSubscriber();
        $request = new Request();
        $request->attributes->set(RequestCorrelationIdProvider::ATTRIBUTE, 'corr-456');
        $response = new Response();

        $subscriber->onResponse($this->createResponseEvent($request, $response));

        self::assertSame('corr-456', $response->headers->get(RequestCorrelationIdProvider::HEADER));
    }

    private function createRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent($this->createKernel(), $request, HttpKernelInterface::MAIN_REQUEST);
    }

    private function createResponseEvent(Request $request, Response $response): ResponseEvent
    {
        return new ResponseEvent($this->createKernel(), $request, HttpKernelInterface::MAIN_REQUEST, $response);
    }

    private function createKernel(): HttpKernelInterface
    {
        return new class() implements HttpKernelInterface {
            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                return new Response();
            }
        };
    }
}
