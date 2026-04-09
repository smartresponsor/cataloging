<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryBulkInterface;
use App\ServiceInterface\CategoryServiceInterface as CategoryCategoryService;
/**
 * Provides the category bulk application service.
 */
final class CategoryBulk implements CategoryBulkInterface
{
    /**
     * Initializes the category bulk service collaborators.
     */
    public function __construct(private CategoryCategoryService $service)
    {
    }

    /**
     * @param list<array<string,mixed>> $ops
     *
     * @return array{accepted:int,rejected:int,results:list<array<string,mixed>>}
     */
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

    /**
     * @param array<string,mixed> $op
     *
     * @return array<string,mixed>
     */
    private function dispatch(string $actorId, array $op): array
    {
        $operation = $this->requiredString($op, 'op');
        $payload = $this->requiredMap($op, 'payload');

        return match ($operation) {
            'create' => $this->service->create(
                $actorId,
                $this->requiredString($payload, 'taxonomyId'),
                $this->optionalString($payload, 'parentId'),
                $this->requiredStringMap($payload, 'name'),
                $this->requiredStringMap($payload, 'slug'),
                $this->optionalMetaMap($payload, 'meta'),
            ),
            'move' => $this->service->move(
                $actorId,
                $this->requiredString($payload, 'id'),
                $this->optionalString($payload, 'parentId'),
                $this->intValue($payload, 'order'),
            ),
            'attach' => $this->attachOperation($actorId, $payload),
            'detach' => $this->detachOperation($actorId, $payload),
            default => throw new \InvalidArgumentException('Unknown op: '.$operation),
        };
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,mixed>
     */
    private function attachOperation(string $actorId, array $payload): array
    {
        $this->service->attach(
            $actorId,
            $this->requiredString($payload, 'id'),
            $this->requiredString($payload, 'targetDomain'),
            $this->requiredString($payload, 'targetClass'),
            $this->requiredString($payload, 'targetId'),
        );

        return ['status' => 'attached'];
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,mixed>
     */
    private function detachOperation(string $actorId, array $payload): array
    {
        $this->service->detach(
            $actorId,
            $this->requiredString($payload, 'id'),
            $this->requiredString($payload, 'targetDomain'),
            $this->requiredString($payload, 'targetClass'),
            $this->requiredString($payload, 'targetId'),
        );

        return ['status' => 'detached'];
    }

    /** @param array<string,mixed> $payload */
    private function requiredString(array $payload, string $key): string
    {
        if (!array_key_exists($key, $payload) || !is_scalar($payload[$key])) {
            throw new \InvalidArgumentException('Missing payload key: '.$key);
        }

        return (string) $payload[$key];
    }

    /** @param array<string,mixed> $payload */
    private function optionalString(array $payload, string $key): ?string
    {
        if (!array_key_exists($key, $payload)) {
            return null;
        }
        $value = $payload[$key];

        return is_scalar($value) ? (string) $value : null;
    }

    /** @param array<string,mixed> $payload */
    private function intValue(array $payload, string $key): int
    {
        $value = $payload[$key] ?? 0;

        return is_numeric($value) ? (int) $value : 0;
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,mixed>
     */
    private function requiredMap(array $payload, string $key): array
    {
        $value = $payload[$key] ?? null;
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Missing payload key: '.$key);
        }

        return $value;
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,string>
     */
    private function requiredStringMap(array $payload, string $key): array
    {
        $map = $this->requiredMap($payload, $key);
        $normalized = [];
        foreach ($map as $entryKey => $entryValue) {
            if (!is_string($entryKey) || !is_scalar($entryValue)) {
                continue;
            }
            $normalized[$entryKey] = (string) $entryValue;
        }

        return $normalized;
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,array<string,bool|float|int|string|null>|bool|float|int|string|null>
     */
    private function optionalMetaMap(array $payload, string $key): array
    {
        $value = $payload[$key] ?? [];
        if (!is_array($value)) {
            return [];
        }
        $normalized = [];
        foreach ($value as $entryKey => $entryValue) {
            if (!is_string($entryKey)) {
                continue;
            }
            if (is_array($entryValue)) {
                $nested = [];
                foreach ($entryValue as $nestedKey => $nestedValue) {
                    if (
                        is_string($nestedKey) &&
                        (is_bool($nestedValue)
                || is_float($nestedValue)
                || is_int($nestedValue)
                || is_string($nestedValue)
                || null === $nestedValue)
                    ) {
                        $nested[$nestedKey] = $nestedValue;
                    }
                }
                $normalized[$entryKey] = $nested;
                continue;
            }
            if (is_bool($entryValue)
            || is_float($entryValue)
            || is_int($entryValue)
            || is_string($entryValue)
            || null === $entryValue) {
                $normalized[$entryKey] = $entryValue;
            }
        }

        return $normalized;
    }
}
