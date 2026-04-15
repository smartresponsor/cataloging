<?php

declare(strict_types=1);

namespace App\Tests\Category\Api;

use App\Controller\CatalogCategoryApiController;
use App\Dto\CatalogCategoryMoveMutationResult;
use App\Dto\CatalogCategoryPublishMutationResult;
use App\Service\CatalogCategoryMutationAuthorizationService;
use App\Service\CatalogCategoryMutationRequestContextResolver;
use App\ServiceInterface\CatalogCategoryMutationServiceInterface;
use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\ServiceInterface\CategoryReadScopeServiceInterface;
use App\ValueObject\CatalogCategoryMutationMoveRequest;
use App\ValueObject\CatalogCategoryMutationPublishRequest;
use App\ValueObject\CatalogCategoryMutationPolicy;
use App\ValueObject\CategoryProjectionCriteria;
use App\ValueObject\CategoryReadScopeRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class CatalogCategoryApiControllerTest extends TestCase
{
    public function testMoveReturns400ForInvalidPayload(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->expects(self::never())->method('assertCanMove');
        $service->expects(self::never())->method('move');

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode([], JSON_THROW_ON_ERROR));

        $response = $controller->move('cat-1', $request);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('{"error":["parent_id is required"]}', $response->getContent());
    }

    public function testMoveReturns200AndMapsPolicyEnum(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->expects(self::once())->method('assertCanMove')->with('cat-1');
        $context->method('actorId')->willReturn('owner-1');
        $context->method('idempotencyKey')->willReturn('idem-1');
        $context->method('correlationId')->willReturn('corr-1');

        $service
            ->expects(self::once())
            ->method('move')
            ->with(self::callback(static function (CatalogCategoryMutationMoveRequest $request): bool {
                return CatalogCategoryMutationPolicy::STRICT === $request->policy()
                    && 'owner-1' === $request->actorId()
                    && 'idem-1' === $request->idempotencyKey();
            }))
            ->willReturn(new CatalogCategoryMoveMutationResult('cat-1', 'root', 'new-parent', 'catalog', 'strict', 1, false, [], false));

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode(['parent_id' => 'new-parent'], JSON_THROW_ON_ERROR));

        $response = $controller->move('cat-1', $request);

        self::assertSame(200, $response->getStatusCode());
    }

    public function testMoveReturns200ForDuplicateIdempotentCommand(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->expects(self::once())->method('assertCanMove')->with('cat-1');
        $context->method('actorId')->willReturn('owner-1');
        $context->method('idempotencyKey')->willReturn('idem-dup');
        $context->method('correlationId')->willReturn('corr-dup');

        $service
            ->expects(self::once())
            ->method('move')
            ->willReturn(new CatalogCategoryMoveMutationResult('cat-1', 'root', 'new-parent', 'catalog', 'strict', 0, false, [], true));

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode(['parent_id' => 'new-parent'], JSON_THROW_ON_ERROR));

        $response = $controller->move('cat-1', $request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{\"data\":{\"id\":\"cat-1\",\"oldParentId\":\"root\",\"newParentId\":\"new-parent\",\"treeId\":\"catalog\",\"policy\":\"strict\",\"changedCount\":0,\"dryRun\":false,\"redirects\":[],\"duplicate\":true}}', $response->getContent());
    }

    public function testPublishReturns400ForInvalidPayload(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->expects(self::never())->method('assertCanPublish');
        $service->expects(self::never())->method('publish');

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode([], JSON_THROW_ON_ERROR));

        $response = $controller->publish('cat-1', $request);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('{\"error\":[\"published is required\"]}', $response->getContent());
    }

    public function testPublishReturns400WhenChecksMissingOnPublishTrue(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->expects(self::never())->method('assertCanPublish');
        $service->expects(self::never())->method('publish');

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode(['published' => true], JSON_THROW_ON_ERROR));

        $response = $controller->publish('cat-1', $request);

        self::assertSame(400, $response->getStatusCode());
        self::assertSame('{\"error\":[\"checks are required when published is true\"]}', $response->getContent());
    }

    public function testPublishReturns200AndMapsContextFields(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $auth->expects(self::once())->method('assertCanPublish')->with('cat-1');
        $context->method('actorId')->willReturn('owner-2');
        $context->method('idempotencyKey')->willReturn('idem-2');
        $context->method('correlationId')->willReturn('corr-2');

        $service
            ->expects(self::once())
            ->method('publish')
            ->with(self::callback(static function (CatalogCategoryMutationPublishRequest $request): bool {
                return true === $request->published()
                    && 'owner-2' === $request->actorId()
                    && 'idem-2' === $request->idempotencyKey()
                    && 'corr-2' === $request->correlationId();
            }))
            ->willReturn(new CatalogCategoryPublishMutationResult(
                'cat-1',
                true,
                'published',
                'approved',
                [],
                [],
                ['slugReady' => true],
                '2026-01-01 00:00:00',
                'manual',
                false,
            ));

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(content: json_encode([
            'published' => true,
            'checks' => ['slugReady' => true],
            'reason' => 'manual',
        ], JSON_THROW_ON_ERROR));

        $response = $controller->publish('cat-1', $request);

        self::assertSame(200, $response->getStatusCode());
    }

    public function testTreeReturnsDataFromScopedReadService(): void
    {
        $service = $this->createMock(CatalogCategoryMutationServiceInterface::class);
        $auth = $this->createMock(CatalogCategoryMutationAuthorizationService::class);
        $read = $this->createMock(CategoryProjectionReadServiceInterface::class);
        $scope = $this->createMock(CategoryReadScopeServiceInterface::class);
        $context = $this->createMock(CatalogCategoryMutationRequestContextResolver::class);

        $criteria = CategoryProjectionCriteria::fromArray(['tenant' => 'tenant-1']);
        $scope
            ->expects(self::once())
            ->method('applyTenantScope')
            ->with(self::callback(static fn (CategoryReadScopeRequest $request): bool => 'tenant-1' === $request->request()->query->get('tenant')))
            ->willReturn($criteria);

        $read
            ->expects(self::once())
            ->method('tree')
            ->with($criteria)
            ->willReturn([['id' => 'cat-1']]);

        $controller = new CatalogCategoryApiController($service, $auth, $read, $scope, $context);
        $request = new Request(query: ['tenant' => 'tenant-1']);

        $response = $controller->tree($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{\"data\":[{\"id\":\"cat-1\"}]}', $response->getContent());
    }
}
