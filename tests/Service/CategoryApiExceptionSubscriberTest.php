<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service;

use App\Cataloging\Exception\CategoryNotFoundException;
use App\Cataloging\Subscriber\CategoryApiExceptionSubscriber;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class CategoryApiExceptionSubscriberTest extends TestCase
{
    public function testIgnoresNonCategoryRoutes(): void
    {
        $subscriber = new CategoryApiExceptionSubscriber();
        $event = $this->event('api_other_route', new \RuntimeException('boom'));

        $subscriber->onKernelException($event);

        self::assertNull($event->getResponse());
    }

    public function testMapsAccessDeniedTo403(): void
    {
        $subscriber = new CategoryApiExceptionSubscriber();
        $event = $this->event('api_category_move', new AccessDeniedHttpException('denied'));

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(403, $response->getStatusCode());
    }

    public function testMapsRuntimeNotFoundTo404(): void
    {
        $subscriber = new CategoryApiExceptionSubscriber();
        $event = $this->event('api_category_publish', new CategoryNotFoundException('category was not found'));

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(404, $response->getStatusCode());
    }

    public function testMapsRuntimeConflictTo409(): void
    {
        $subscriber = new CategoryApiExceptionSubscriber();
        $event = $this->event('api_category_publish', new \RuntimeException('conflict'));

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(409, $response->getStatusCode());
    }

    public function testMapsInvalidArgumentTo400(): void
    {
        $subscriber = new CategoryApiExceptionSubscriber();
        $event = $this->event('api_category_move', new \InvalidArgumentException('bad input'));

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(400, $response->getStatusCode());
    }

    public function testMapsDomainExceptionTo409(): void
    {
        $subscriber = new CategoryApiExceptionSubscriber();
        $event = $this->event('api_category_move', new \DomainException('domain conflict'));

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(409, $response->getStatusCode());
    }

    public function testMapsDbalExceptionTo500(): void
    {
        $subscriber = new CategoryApiExceptionSubscriber();
        $event = $this->event('api_category_tree', $this->createMock(Exception::class));

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(500, $response->getStatusCode());
        self::assertSame('{"error":"CategoryEntity API runtime failure."}', $response->getContent());
    }

    public function testMapsUnexpectedThrowableTo500(): void
    {
        $subscriber = new CategoryApiExceptionSubscriber();
        $event = $this->event('api_category_tree', new \Error('fatal'));

        $subscriber->onKernelException($event);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame(500, $response->getStatusCode());
        self::assertSame('{"error":"CategoryEntity API unexpected failure."}', $response->getContent());
    }

    private function event(string $route, \Throwable $throwable): ExceptionEvent
    {
        $kernel = $this->createMock(KernelInterface::class);
        $request = new Request();
        $request->attributes->set('_route', $route);

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $throwable);
    }
}
