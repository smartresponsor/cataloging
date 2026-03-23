<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryBulkInterface;
use App\ServiceInterface\CategoryInterface as CategoryService;

final class CategoryBulk implements CategoryBulkInterface
{
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function execute(string $actorId, string $batchKey, array $ops): array
    {
        $accepted = 0;
        $rejected = 0;
        $results = [];
        foreach ($ops as $index => $op) {
            try {
                $results[] = $this->dispatch($actorId, $op);
                ++$accepted;
            } catch (\RuntimeException|\InvalidArgumentException|\TypeError $e) {
                ++$rejected;
                error_log('[CategoryBulk] '.$e->getMessage());
                $results[] = ['index' => $index, 'error' => $e->getMessage()];
            }
        }

        return ['accepted' => $accepted, 'rejected' => $rejected, 'results' => $results];
    }

    /** @param array<string,mixed> $op */
    private function dispatch(string $actorId, array $op): array
    {
        $operation = $op['op'] ?? null;
        $payload = $op['payload'] ?? null;
        if (!is_string($operation) || !is_array($payload)) {
            throw new \InvalidArgumentException('Invalid bulk operation envelope');
        }

        switch ($operation) {
            case 'create':
                return $this->service->create(
                    $actorId,
                    (string) $this->require($payload, 'taxonomyId'),
                    isset($payload['parentId']) ? (string) $payload['parentId'] : null,
                    (array) $this->require($payload, 'name'),
                    (array) $this->require($payload, 'slug'),
                    (array) ($payload['meta'] ?? [])
                );
            case 'move':
                return $this->service->move(
                    $actorId,
                    (string) $this->require($payload, 'id'),
                    isset($payload['parentId']) ? (string) $payload['parentId'] : null,
                    (int) ($payload['order'] ?? 0)
                );
            case 'attach':
                $this->service->attach(
                    $actorId,
                    (string) $this->require($payload, 'id'),
                    (string) $this->require($payload, 'targetDomain'),
                    (string) $this->require($payload, 'targetClass'),
                    (string) $this->require($payload, 'targetId')
                );

                return ['status' => 'attached'];
            case 'detach':
                $this->service->detach(
                    $actorId,
                    (string) $this->require($payload, 'id'),
                    (string) $this->require($payload, 'targetDomain'),
                    (string) $this->require($payload, 'targetClass'),
                    (string) $this->require($payload, 'targetId')
                );

                return ['status' => 'detached'];
        }

        throw new \InvalidArgumentException('Unknown op: '.$operation);
    }

    /** @param array<string,mixed> $payload */
    private function require(array $payload, string $key): mixed
    {
        if (!array_key_exists($key, $payload)) {
            throw new \InvalidArgumentException('Missing payload key: '.$key);
        }

        return $payload[$key];
    }
}
