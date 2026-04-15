<?php

declare(strict_types=1);

namespace App\Tests\Category\Api;

use App\Controller\CatalogCategoryApiController;
use App\Exception\CatalogCategoryNotFoundException;
use App\Service\CatalogCategoryMutationAuthorizationService;
use App\Service\CatalogCategoryMutationRequestContextResolver;
use App\ServiceInterface\CatalogCategoryMutationServiceInterface;
use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\ServiceInterface\CategoryReadScopeServiceInterface;
use App\Subscriber\CatalogCategoryApiExceptionSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class CatalogCategoryApiErrorIntegrationTest extends TestCase
{
    public function testMoveDomainExceptionIsSerializedBySubscriber(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->method('assertCanMove')->willThrowException(new \DomainException('conflict'));

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode(['parent_id' => 'new-parent'], JSON_THROW_ON_ERROR));
        $request->attributes->set('_route', 'api_catalog_category_move');

        try {
            $controller->move('cat-1', $request);
            self::fail('Expected DomainException to be thrown by controller flow.');
        } catch (\Throwable $throwable) {
            $event = $this->exceptionEvent($request, $throwable);
            (new CatalogCategoryApiExceptionSubscriber())->onKernelException($event);

            self::assertSame(409, $event->getResponse()?->getStatusCode());
            self::assertSame('{"error":"conflict"}', $event->getResponse()?->getContent());
        }
    }

    public function testPublishRuntimeNotFoundIsSerializedBySubscriber(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->method('assertCanPublish')->willReturn(null);
        $context->method('actorId')->willReturn('owner');
        $context->method('idempotencyKey')->willReturn(null);
        $context->method('correlationId')->willReturn(null);
        $service->method('publish')->willThrowException(new CatalogCategoryNotFoundException('entity was not found'));

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode(['published' => false], JSON_THROW_ON_ERROR));
        $request->attributes->set('_route', 'api_catalog_category_publish');

        try {
            $controller->publish('cat-1', $request);
            self::fail('Expected RuntimeException to be thrown by controller flow.');
        } catch (\Throwable $throwable) {
            $event = $this->exceptionEvent($request, $throwable);
            (new CatalogCategoryApiExceptionSubscriber())->onKernelException($event);

            self::assertSame(404, $event->getResponse()?->getStatusCode());
            self::assertSame('{"error":"entity was not found"}', $event->getResponse()?->getContent());
        }
    }

    public function testMoveAccessDeniedIsSerializedBySubscriber(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->method('assertCanMove')->willThrowException(new AccessDeniedHttpException('denied'));

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode(['parent_id' => 'new-parent'], JSON_THROW_ON_ERROR));
        $request->attributes->set('_route', 'api_catalog_category_move');

        try {
            $controller->move('cat-1', $request);
            self::fail('Expected AccessDeniedHttpException to be thrown by controller flow.');
        } catch (\Throwable $throwable) {
            $event = $this->exceptionEvent($request, $throwable);
            (new CatalogCategoryApiExceptionSubscriber())->onKernelException($event);

            self::assertSame(403, $event->getResponse()?->getStatusCode());
            self::assertSame('{\"error\":\"denied\"}', $event->getResponse()?->getContent());
        }
    }

    public function testMoveInvalidArgumentIsSerializedBySubscriber(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->method('assertCanMove')->willReturn(null);
        $context->method('actorId')->willReturn('owner');
        $context->method('idempotencyKey')->willReturn(null);
        $context->method('correlationId')->willReturn(null);
        $service->method('move')->willThrowException(new \InvalidArgumentException('bad request'));

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode(['parent_id' => 'new-parent'], JSON_THROW_ON_ERROR));
        $request->attributes->set('_route', 'api_catalog_category_move');

        try {
            $controller->move('cat-1', $request);
            self::fail('Expected InvalidArgumentException to be thrown by controller flow.');
        } catch (\Throwable $throwable) {
            $event = $this->exceptionEvent($request, $throwable);
            (new CatalogCategoryApiExceptionSubscriber())->onKernelException($event);

            self::assertSame(400, $event->getResponse()?->getStatusCode());
            self::assertSame('{\"error\":\"bad request\"}', $event->getResponse()?->getContent());
        }
    }

    public function testTreeUnexpectedThrowableIsSerializedBySubscriber(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $scope->method('applyTenantScope')->willThrowException(new \Error('fatal'));

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(query: ['tenant' => 'tenant-1']);
        $request->attributes->set('_route', 'api_catalog_category_tree');

        try {
            $controller->tree($request);
            self::fail('Expected Error to be thrown by controller flow.');
        } catch (\Throwable $throwable) {
            $event = $this->exceptionEvent($request, $throwable);
            (new CatalogCategoryApiExceptionSubscriber())->onKernelException($event);

            self::assertSame(500, $event->getResponse()?->getStatusCode());
            self::assertSame('{\"error\":\"Catalog category API unexpected failure.\"}', $event->getResponse()?->getContent());
        }
    }

    private function exceptionEvent(Request $request, \Throwable $throwable): ExceptionEvent
    {
        $kernel = $this->createMock(KernelInterface::class);

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $throwable);
    }
}
