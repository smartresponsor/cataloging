<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Controller\Admin;

use App\ServiceInterface\Command\Category\CategoryMovePolicy;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class CategoryMoveController
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly CategoryMoveInterface $service,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function move(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent(), true);
        if (!is_array($body)) {
            return new JsonResponse([
                'ok' => false,
                'error' => 'The request body must be valid JSON.',
            ], 400);
        }

        $nodeId = trim((string) ($body['nodeId'] ?? ''));
        $treeId = trim((string) ($body['treeId'] ?? 'default'));
        $newParentId = trim((string) ($body['newParentId'] ?? ''));
        $policy = (string) ($body['policy'] ?? CategoryMovePolicy::PRESERVE_SLUG);
        $dryRun = (bool) ($body['dryRun'] ?? false);
        $locale = isset($body['locale']) ? (string) $body['locale'] : null;

        if ('' === $nodeId) {
            return new JsonResponse([
                'ok' => false,
                'error' => 'The category identifier is required.',
            ], 400);
        }

        try {
            [$count, $redirects] = $this->service->move($nodeId, $newParentId, $treeId, $policy, $dryRun, $locale);

            return new JsonResponse([
                'ok' => true,
                'changedCount' => $count,
                'warnings' => [],
                'redirects' => $redirects,
                'dryRun' => $dryRun,
                'message' => $dryRun
                    ? 'The move preview was generated successfully.'
                    : 'The category was moved successfully.',
            ]);
        } catch (\Throwable $throwable) {
            $this->logger->error('Admin category move request failed.', [
                'nodeId' => $nodeId,
                'treeId' => $treeId,
                'newParentId' => $newParentId,
                'policy' => $policy,
                'dryRun' => $dryRun,
                'locale' => $locale,
                'exception' => $throwable,
            ]);

            return new JsonResponse([
                'ok' => false,
                'error' => 'The category could not be moved.',
                'message' => 'Please review the request and try again. Check the logs if the problem continues.',
            ], 500);
        }
    }
}
