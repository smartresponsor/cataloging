<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Command\Category;

use App\ServiceInterface\Command\Category\CategoryCommandServiceInterface as CategoryService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class CatalogCategoryBulk implements CategoryBulkInterface
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly CategoryService $service,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function execute(string $actorId, string $batchKey, array $ops): array
    {
        $accepted = 0;
        $rejected = 0;
        $results = [];

        foreach ($ops as $index => $op) {
            try {
                switch ($op['op']) {
                    case 'create':
                        $payload = $op['payload'];
                        $results[] = $this->service->create(
                            $actorId,
                            (string) $payload['taxonomyId'],
                            $payload['parentId'] ?? null,
                            (array) $payload['name'],
                            (array) $payload['slug'],
                            (array) ($payload['meta'] ?? []),
                        );
                        ++$accepted;
                        break;
                    case 'move':
                        $payload = $op['payload'];
                        $results[] = $this->service->move(
                            $actorId,
                            (string) $payload['id'],
                            $payload['parentId'] ?? null,
                            (int) ($payload['order'] ?? 0),
                        );
                        ++$accepted;
                        break;
                    case 'attach':
                        $payload = $op['payload'];
                        $this->service->attach(
                            $actorId,
                            (string) $payload['id'],
                            (string) $payload['targetDomain'],
                            (string) $payload['targetClass'],
                            (string) $payload['targetId'],
                        );
                        ++$accepted;
                        break;
                    case 'detach':
                        $payload = $op['payload'];
                        $this->service->detach(
                            $actorId,
                            (string) $payload['id'],
                            (string) $payload['targetDomain'],
                            (string) $payload['targetClass'],
                            (string) $payload['targetId'],
                        );
                        ++$accepted;
                        break;
                    default:
                        $rejected++;
                        $results[] = [
                            'ok' => false,
                            'message' => 'The bulk operation contains an unsupported action.',
                            'op' => (string) ($op['op'] ?? 'unknown'),
                            'index' => $index,
                        ];
                }
            } catch (\Throwable $throwable) {
                ++$rejected;
                $results[] = [
                    'ok' => false,
                    'message' => $this->humanMessage($throwable, 'The bulk operation item failed.'),
                    'op' => (string) ($op['op'] ?? 'unknown'),
                    'index' => $index,
                ];

                $this->logger->error('Category bulk operation failed.', [
                    'actorId' => $actorId,
                    'batchKey' => $batchKey,
                    'index' => $index,
                    'operation' => $op['op'] ?? null,
                    'exception' => $throwable,
                ]);
            }
        }

        return [
            'accepted' => $accepted,
            'rejected' => $rejected,
            'results' => $results,
        ];
    }

    private function humanMessage(\Throwable $throwable, string $fallback): string
    {
        $message = trim($throwable->getMessage());

        if ('' === $message) {
            return $fallback;
        }

        return rtrim(ucfirst($message), '.').'.';
    }
}
