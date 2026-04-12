<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryBulkInterface;
use App\ServiceInterface\CategoryServiceInterface as CategoryCategoryService;
use App\ValueObject\CategoryCreateRequest;
use App\ValueObject\CategoryLinkRequest;
use App\ValueObject\CategoryServiceMoveRequest;

/**
 * Provides the category bulk application service.
 */
/** @noinspection DuplicatedCode */
final readonly class CategoryBulk implements CategoryBulkInterface
{
    /**
     * Initializes the category bulk service collaborators.
     */
    public function __construct(
        private CategoryCategoryService $service,
        private MetaPayloadNormalizer $metaPayloadNormalizer,
    ) {
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
            } catch (\RuntimeException|\InvalidArgumentException|\TypeError $exception) {
                ++$rejected;
                error_log('[CategoryBulk] '.$exception->getMessage());
                $results[] = ['index' => $index, 'error' => $exception->getMessage()];
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
            'create' => $this->service->create($this->createRequest($actorId, $payload)),
            'move' => $this->service->move(new CategoryServiceMoveRequest(
                $actorId,
                $this->requiredString($payload, 'id'),
                $this->optionalString($payload, 'parentId'),
                $this->intValue($payload, 'order'),
            )),
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
        $this->service->attach($this->linkRequest($actorId, $payload));

        return ['status' => 'attached'];
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,mixed>
     */
    private function detachOperation(string $actorId, array $payload): array
    {
        $this->service->detach($this->linkRequest($actorId, $payload));

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

    /**
     * @param array<string,mixed> $payload
     *
     * @noinspection PhpSameParameterValueInspection
     */
    private function optionalString(array $payload, string $key): ?string
    {
        if (!array_key_exists($key, $payload)) {
            return null;
        }
        $value = $payload[$key];

        return is_scalar($value) ? (string) $value : null;
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @noinspection PhpSameParameterValueInspection
     */
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
    private function metaMap(array $payload): array
    {
        return $this->metaPayloadNormalizer->normalize($payload['meta'] ?? []);
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array<string,array<string,bool|float|int|string|null>|bool|float|int|string|null>
     */
    private function metaPayload(array $payload): array
    {
        return $this->metaMap($payload);
    }

    /** @param array<string,mixed> $payload */
    private function createRequest(string $actorId, array $payload): CategoryCreateRequest
    {
        return new CategoryCreateRequest(
            $actorId,
            $this->requiredString($payload, 'taxonomyId'),
            $this->optionalString($payload, 'parentId'),
            $this->requiredStringMap($payload, 'name'),
            $this->requiredStringMap($payload, 'slug'),
            $this->metaPayload($payload),
        );
    }

    /** @param array<string,mixed> $payload */
    private function linkRequest(string $actorId, array $payload): CategoryLinkRequest
    {
        return new CategoryLinkRequest(
            $actorId,
            $this->requiredString($payload, 'id'),
            $this->requiredString($payload, 'targetDomain'),
            $this->requiredString($payload, 'targetClass'),
            $this->requiredString($payload, 'targetId'),
        );
    }
}
