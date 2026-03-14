<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Controller;

use App\Request\MoveCategoryRequest;
use App\Request\PublishCategoryRequest;
use App\Service\Query\Category\Status;
use App\Service\Workflow\Category\PublishOperation;
use App\ServiceInterface\Command\Category\CategoryMovePolicy;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogApiController
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly CategoryQueryServiceInterface $queryService,
        private readonly CategoryMoveInterface $moveService,
        private readonly PublishOperation $publishOperation,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    #[Route('/api/catalog/tree', name: 'api_category_tree', methods: ['GET'])]
    public function tree(Request $request): JsonResponse
    {
        $channel = (string) $request->query->get('channel', 'default');
        $locale = (string) $request->query->get('locale', 'en');

        try {
            return new JsonResponse([
                'data' => $this->queryService->list(['channel' => $channel, 'locale' => $locale]),
                'channel' => $channel,
                'locale' => $locale,
                'message' => 'The category tree was loaded successfully.',
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Category tree query failed.', [
                'channel' => $channel,
                'locale' => $locale,
                'exception' => $throwable,
            ]);

            return new JsonResponse([
                'error' => 'The category tree could not be loaded.',
                'message' => 'Please try again. Check the logs if the problem continues.',
            ], 500);
        }
    }

    #[Route('/api/catalog/{id}/move', name: 'api_category_move', methods: ['POST'])]
    public function move(string $id, Request $request): JsonResponse
    {
        $dto = MoveCategoryRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        if (!$dto->isValid()) {
            return new JsonResponse([
                'error' => 'The move request is invalid.',
                'details' => $dto->getErrors(),
            ], 400);
        }

        try {
            [$changedCount, $redirects] = $this->moveService->move(
                $id,
                (string) ($dto->parentId ?? ''),
                'api',
                CategoryMovePolicy::PRESERVE_SLUG,
                $dto->dryRun,
                $dto->locale,
            );

            return new JsonResponse([
                'status' => 'ok',
                'changedCount' => $changedCount,
                'redirects' => $redirects,
                'dryRun' => $dto->dryRun,
                'message' => $dto->dryRun
                    ? 'The move preview was generated successfully.'
                    : 'The category was moved successfully.',
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Category move API request failed.', [
                'id' => $id,
                'parentId' => $dto->parentId,
                'locale' => $dto->locale,
                'dryRun' => $dto->dryRun,
                'exception' => $throwable,
            ]);

            return new JsonResponse([
                'error' => 'The category could not be moved.',
                'message' => 'Please review the request and try again. Check the logs if the problem continues.',
            ], 500);
        }
    }

    #[Route('/api/catalog/{id}/publish', name: 'api_category_publish', methods: ['POST'])]
    public function publish(string $id, Request $request): JsonResponse
    {
        $dto = PublishCategoryRequest::fromArray(json_decode($request->getContent(), true) ?? []);
        if (!$dto->isValid()) {
            return new JsonResponse([
                'error' => 'The publish request is invalid.',
                'details' => $dto->getErrors(),
            ], 400);
        }

        if (true !== $dto->published) {
            return new JsonResponse([
                'error' => 'The publish request must explicitly set published=true.',
            ], 400);
        }

        try {
            $status = $this->publishOperation->publish(new Status(Status::DRAFT));

            return new JsonResponse([
                'status' => $status->value(),
                'id' => $id,
                'message' => 'The category was published successfully.',
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Category publish API request failed.', [
                'id' => $id,
                'exception' => $throwable,
            ]);

            return new JsonResponse([
                'error' => 'The category could not be published.',
                'message' => 'Please try again. Check the logs if the problem continues.',
            ], 500);
        }
    }
}
