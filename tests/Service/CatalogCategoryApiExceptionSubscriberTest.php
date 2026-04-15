<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\CatalogCategoryNotFoundException;
use App\Subscriber\CatalogCategoryApiExceptionSubscriber;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class CatalogCategoryApiExceptionSubscriberTest extends TestCase
{
    public function testIgnoresNonCatalogCategoryRoutes(): void
    {
        $subscriber = new CatalogCategoryApiExceptionSubscriber();
        $event = $this->event('api_other_route', new \RuntimeException('boom'));

        $subscriber->onKernelException($event);

        self::assertNull($event->getResponse());
    }

    public function testMapsAccessDeniedTo403(): void
    {
        $subscriber = new CatalogCategoryApiExceptionSubscriber();
        $event = $this->event('api_catalog_category_move', new AccessDeniedHttpException('denied'));

        $subscriber->onKernelException($event);

        self::assertSame(403, $event->getResponse()?->getStatusCode());
    }

    public function testMapsRuntimeNotFoundTo404(): void
    {
        $subscriber = new CatalogCategoryApiExceptionSubscriber();
        $event = $this->event('api_catalog_category_publish', new CatalogCategoryNotFoundException('category was not found'));

        $subscriber->onKernelException($event);

        self::assertSame(404, $event->getResponse()?->getStatusCode());
    }

    public function testMapsRuntimeConflictTo409(): void
    {
        $subscriber = new CatalogCategoryApiExceptionSubscriber();
        $event = $this->event('api_catalog_category_publish', new \RuntimeException('conflict'));

        $subscriber->onKernelException($event);

        self::assertSame(409, $event->getResponse()?->getStatusCode());
    }

    public function testMapsInvalidArgumentTo400(): void
    {
        $subscriber = new CatalogCategoryApiExceptionSubscriber();
        $event = $this->event('api_catalog_category_move', new \InvalidArgumentException('bad input'));

        $subscriber->onKernelException($event);

        self::assertSame(400, $event->getResponse()?->getStatusCode());
    }

    public function testMapsDomainExceptionTo409(): void
    {
        $subscriber = new CatalogCategoryApiExceptionSubscriber();
        $event = $this->event('api_catalog_category_move', new \DomainException('domain conflict'));

        $subscriber->onKernelException($event);

        self::assertSame(409, $event->getResponse()?->getStatusCode());
    }

    public function testMapsDbalExceptionTo500(): void
    {
        $subscriber = new CatalogCategoryApiExceptionSubscriber();
        $event = $this->event('api_catalog_category_tree', $this->createMock(Exception::class));

        $subscriber->onKernelException($event);

        self::assertSame(500, $event->getResponse()?->getStatusCode());
        self::assertSame('{"error":"Catalog category API runtime failure."}', $event->getResponse()?->getContent());
    }

    public function testMapsUnexpectedThrowableTo500(): void
    {
        $subscriber = new CatalogCategoryApiExceptionSubscriber();
        $event = $this->event('api_catalog_category_tree', new \Error('fatal'));

        $subscriber->onKernelException($event);

        self::assertSame(500, $event->getResponse()?->getStatusCode());
        self::assertSame('{"error":"Catalog category API unexpected failure."}', $event->getResponse()?->getContent());
    }

    private function event(string $route, \Throwable $throwable): ExceptionEvent
    {
        $kernel = $this->createMock(KernelInterface::class);
        $request = new Request();
        $request->attributes->set('_route', $route);

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $throwable);
    }
}
